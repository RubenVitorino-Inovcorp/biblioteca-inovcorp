<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Loan::class, 'loan');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $loans = Loan::query()
            ->with(['book', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('loan_number', 'like', "%{$search}%")
                      ->orWhereHas('book', function ($bookQuery) use ($search) {
                           $bookQuery->where('title', 'like', "%{$search}%");
                       })
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->sort, function ($query, $sort) {
                match ($sort) {
                    'inicio_recente' => $query->orderBy('start_date', 'desc'),
                    'inicio_antigo' => $query->orderBy('start_date', 'asc'),
                    'devolucao_proxima' => $query->orderBy('estimated_return_date', 'asc'),
                    'devolucao_distante' => $query->orderBy('estimated_return_date', 'desc'),
                    'numero_asc' => $query->orderBy('loan_number', 'asc'),
                    'numero_desc' => $query->orderBy('loan_number', 'desc'),
                    'dias_asc' => $query->orderByRaw('julianday(COALESCE(end_date, CURRENT_DATE)) - julianday(start_date) ASC'),
                    'dias_desc' => $query->orderByRaw('julianday(COALESCE(end_date, CURRENT_DATE)) - julianday(start_date) DESC'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(15, ['*'], 'pag')
            ->withQueryString();

        $filters = $request->only(['search', 'sort', 'status']);

        if (!in_array($filters['sort'] ?? null, ['inicio_recente', 'inicio_antigo', 'devolucao_proxima', 'devolucao_distante', 'numero_asc', 'numero_desc', 'dias_asc', 'dias_desc'], true)) {
            $filters['sort'] = '';
        }

        $stats = [
            'active_loans' => Loan::whereIn('status', [LoanStatus::ACTIVE, LoanStatus::OVERDUE])->count(),
            'last_30_days' => Loan::whereNotNull('start_date')->where('start_date', '>=', now()->subDays(30))->count(),
            'returned_today' => Loan::where('status', LoanStatus::RETURNED)->whereDate('end_date', now()->toDateString())->count(),
        ];

        return Inertia::render('Loans/Index', [
            'loans' => $loans,
            'filters' => $filters,
            'stats' => $stats,
            'books' => Book::query()->orderBy('title')->get(),
            'pending_loans' => Loan::query()->where('status', LoanStatus::PENDING)->with('book', 'user')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Loans/Create', [
            'books' => Book::query()->orderBy('title')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $book = Book::findOrFail($validated['book_id']);

        if (!$user->canMakeLoans()) {
            throw ValidationException::withMessages([
                'user_id' => 'Este utilizador já atingiu o limite de 3 requisições!',
            ]);
        }

        $alreadyHasBook = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', [LoanStatus::ACTIVE, LoanStatus::OVERDUE])
            ->exists();

        if ($alreadyHasBook) {
            throw ValidationException::withMessages([
                'book_id' => 'Este utilizador já tem uma requisição ativa para este livro.',
            ]);
        }

        if (!$book->is_available) {
            throw ValidationException::withMessages([
                'book_id' => 'Este livro não está disponível.',
            ]);
        }

        DB::transaction(function () use ($user, $book) {
            Loan::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'user_photo_snapshot' => $user->profile_photo_url,
                'start_date' => now(),
                'estimated_return_date' => now()->addDays(5),
                'status' => LoanStatus::ACTIVE,
            ]);

            $book->decrement('available_stock');
        });

        return redirect()->route('requisicoes.index')->with('success', 'Requisição criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        return Inertia::render('Loans/Show', [
            'loan' => $loan->load('book.authors', 'book.publisher', 'user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        return Inertia::render('Loans/Edit', [
            'loan' => $loan->load('book', 'user'),
            'books' => Book::query()->orderBy('title')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_column(LoanStatus::cases(), 'value')),
            'end_date' => 'nullable|date',
        ]);

        $loan->update($validated);

        return redirect()->route('requisicoes.index')->with('success', 'Requisição atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        // Restore book stock if loan was active
        if (in_array($loan->status, [LoanStatus::ACTIVE, LoanStatus::OVERDUE])) {
            $loan->book->increment('available_stock');
        }

        $loan->delete();

        return redirect()->route('requisicoes.index')->with('success', 'Requisição excluída com sucesso!');
    }

    /**
     * Approve a pending loan.
     */
    public function approve(Loan $loan)
    {
        Gate::authorize('update', $loan);

        if ($loan->status !== LoanStatus::PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Apenas requisições pendentes podem ser aprovadas.',
            ]);
        }

        $loan->update([
            'start_date' => now(),
            'estimated_return_date' => now()->addDays(5),
            'status' => LoanStatus::ACTIVE,
        ]);

        return redirect()->route('requisicoes.index')->with('success', 'Requisição aprovada com sucesso!');
    }

    /**
     * Reject a pending loan.
     */
    public function reject(Loan $loan)
    {
        Gate::authorize('update', $loan);

        if ($loan->status !== LoanStatus::PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Apenas requisições pendentes podem ser rejeitadas.',
            ]);
        }

        // Restore book stock since the loan is being rejected
        $loan->book->increment('available_stock');

        $loan->update([
            'status' => LoanStatus::REJECTED,
        ]);

        return redirect()->route('requisicoes.index')->with('success', 'Requisição rejeitada.');
    }

    /**
     * Return an active/overdue book.
     */
    public function returnBook(Loan $loan, Request $request)
    {
        Gate::authorize('update', $loan);

        if (!in_array($loan->status, [LoanStatus::ACTIVE, LoanStatus::OVERDUE, LoanStatus::RETURN_PENDING], true)) {
            throw ValidationException::withMessages([
                'status' => 'Esta requisição já se encontra encerrada ou inválida para devolução.',
            ]);
        }   

        $minDate = $loan->start_date ? $loan->start_date->format('Y-m-d') : now()->format('Y-m-d');
        
        $validated = $request->validate([
            'return_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . $minDate,
            ]
        ]);

        DB::transaction(function () use ($loan, $validated) {
            $loan->update([
                'status' => LoanStatus::RETURNED,
                'end_date' => $validated['return_date'],
            ]);

            $loan->book->increment('available_stock');
        });

        return redirect()->route('requisicoes.index')->with('success', "Boa requisição confirmada! Dias decorridos: {$loan->elapsed_days} dias.");
    }
}

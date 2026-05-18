<?php

namespace App\Http\Controllers\User;

use App\Enums\LoanStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Mail\LoanRequestedAdminMail;
use App\Mail\LoanRequestedUserMail;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Loan::class, 'loan');
    }

    /**
     * Display a listing of the authenticated user's loans.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $loans = Loan::query()
            ->where('user_id', $user->id)
            ->with(['book'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('loan_number', 'like', "%{$search}%")
                      ->orWhereHas('book', function ($bookQuery) use ($search) {
                           $bookQuery->where('title', 'like', "%{$search}%");
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

        $activeLoans = Loan::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [LoanStatus::ACTIVE, LoanStatus::OVERDUE])
            ->with(['book', 'user'])
            ->latest()
            ->get();

        return Inertia::render('User/Loans/Index', [
            'loans' => $loans,
            'active_loans' => $activeLoans,
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created loan request.
     * Users create loan requests with PENDING status — admins approve/reject.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $user = Auth::user();
        $book = Book::findOrFail($validated['book_id']);

        $loan = DB::transaction(function () use ($user, $book) {
            if (!$user->canMakeLoans()) {
                throw ValidationException::withMessages([
                    'book_id' => 'Já atingiu o limite de 3 requisições!',
                ]);
            }

            $alreadyHasBook = \App\Models\Loan::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->whereIn('status', [\App\Enums\LoanStatus::ACTIVE, \App\Enums\LoanStatus::OVERDUE, \App\Enums\LoanStatus::PENDING])
                ->exists();

            if ($alreadyHasBook) {
                throw ValidationException::withMessages([
                    'book_id' => 'Já tem uma requisição ativa ou pendente para este livro.',
                ]);
            }

            $book = \App\Models\Book::where('id', $book->id)->lockForUpdate()->first();
            
            if (!$book->is_available) {
                throw ValidationException::withMessages([
                    'book_id' => 'Este livro não está disponível.',
                ]);
            }

            $newLoan = \App\Models\Loan::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'user_photo_snapshot' => $user->profile_photo_url,
                'status' => \App\Enums\LoanStatus::PENDING,
            ]);

            $book->decrement('available_stock');

            return $newLoan;
        });

        $loan->load(['book', 'user']);

        Mail::to($user)->queue(new LoanRequestedUserMail($loan));

        $admins = User::where('role', UserRole::ADMIN->value)->get();
        
        // Manda emails de 3 em 3 segundos para evitar sobrecarga no servidor gratuito do mailtrap
        foreach ($admins as $index => $admin) {
            $delay = 3 + ($index * 3); 
            Mail::to($admin)->later(now()->addSeconds($delay), new LoanRequestedAdminMail($loan));
        }


        return redirect()->route('catalog.requisicoes.index')->with('success', 'Requisição enviada com sucesso! Aguarde aprovação.');
    }

    /**
     * Display the specified loan.
     */
    public function show(Loan $loan)
    {
        return Inertia::render('User/Loans/Show', [
            'loan' => $loan->load('book.authors', 'book.publisher', 'user'),
        ]);
    }

    /**
     * Return an active/overdue book.
     */
    public function returnBook(Loan $loan)
    {
        Gate::authorize('update', $loan);

        if (!in_array($loan->status, [LoanStatus::ACTIVE, LoanStatus::OVERDUE], true)) {
            throw ValidationException::withMessages([
                'status' => 'Esta requisição não pode ser devolvida no estado atual.',
            ]);
        }

        $loan->update([
            'status' => LoanStatus::RETURN_PENDING,
        ]);

        return redirect()->route('catalog.requisicoes.index')->with('success', 'Devolução solicitada com sucesso!');
    }
}

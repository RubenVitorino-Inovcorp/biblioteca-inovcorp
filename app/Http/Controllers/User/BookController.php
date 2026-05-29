<?php

namespace App\Http\Controllers\User;

use App\Enums\LoanStatus;
use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BookController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Book::class, 'book');
    }

    public function index(Request $request)
    {
        // Implementação de pesquisa inteligente com Meilisearch.
        // Extrair e normalizar os filtros (resolver a diferença entre "search" e "filters.search").
        $filters = $request->only(['search', 'sort', 'publisher', 'author']);
        $searchQuery = $filters['search'] ?? $request->input('filters.search');

        // Limpeza estrita de ordenações inválidas
        if (! in_array($filters['sort'] ?? null, ['preco_asc', 'preco_desc', 'titulo_az', 'titulo_za'], true)) {
            $filters['sort'] = '';
        }

        // Definir as regras de SQL que se aplicam a ambos os cenários
        $applySqlFilters = function ($query) use ($filters) {
            $query->with(['authors', 'publisher'])
                ->when($filters['publisher'] ?? null, fn ($q, $pubId) => $q->where('publisher_id', $pubId))
                ->when($filters['author'] ?? null, fn ($q, $authId) => $q->whereHas('authors', fn ($q2) => $q2->where('authors.id', $authId)))
                ->when($filters['sort'] ?? null, function ($q, $sort) {
                    return match ($sort) {
                        'preco_asc' => $q->orderBy('price', 'asc'),
                        'preco_desc' => $q->orderBy('price', 'desc'),
                        'titulo_az' => $q->orderBy('title', 'asc'),
                        'titulo_za' => $q->orderBy('title', 'desc'),
                        default => $q->latest(),
                    };
                }, fn ($q) => $q->latest());
        };

        // Meilisearch ou Eloquent normal no caso de não haver pesquisa texto
        if ($searchQuery) {
            // O Meilisearch filtra o texto; o callback aplica o SQL aos IDs devolvidos
            $books = Book::search($searchQuery)
                ->query($applySqlFilters)
                ->paginate(15, 'pag');
        } else {
            // Sem pesquisa texto, aplica-se as regras diretamente à query base do SQL
            $booksQuery = Book::query();
            $applySqlFilters($booksQuery);

            $books = $booksQuery->paginate(15, ['*'], 'pag');
        }

        // Executar e paginar
        $books->withQueryString();

        return Inertia::render('User/Books/Index', [
            'books' => $books,
            'filters' => $filters,
            'publishers' => Publisher::query()->orderBy('name')->limit(100)->get(),
            'authors' => Author::query()->orderBy('name')->limit(100)->get(),
        ]);
    }

    public function show(Book $book)
    {
        $userId = auth()->id();

        $book->load([
            'authors',
            'publisher',
            'tags',
            'reviews' => function ($query) {
                $query->with('user')->where('status', ReviewStatus::APPROVED)->latest();
            },
        ]);

        $book->has_alert = auth()->check() && DB::table('book_alerts')
            ->where('book_id', $book->id)
            ->where('user_id', $userId)
            ->exists();

        $userReview = $book->reviews()
            ->where('user_id', $userId)
            ->first();

        $reviewableLoanId = null;
        if (! $userReview) {
            $reviewableLoanId = $book->loans()
                ->where('user_id', $userId)
                ->where('status', LoanStatus::RETURNED)
                ->whereDoesntHave('review')
                ->value('id');
        }

        $keywords = Str::words($book->bibliography, 15, '');
        $similarityQuery = "{$book->title} {$keywords}";

        $relatedBooks = Book::search($similarityQuery)
            ->query(fn ($q) => $q->with(['authors'])) // Evita N+1 na renderização das sugestões
            ->take(6) // Pede 6 livros para termos a certeza que temos 5 (o livro atual será quase sempre o resultado #1)
            ->get()
            ->reject(fn ($b) => $b->id === $book->id) // Remove o próprio livro da lista
            ->take(5) // Garante que o frontend recebe um máximo de 5 sugestões
            ->values();

        return Inertia::render('User/Books/Show', [
            'book' => $book,
            'relatedBooks' => $relatedBooks,
            'loans' => $book->loans()->with('user')->latest()->get(),
            'userReview' => $userReview,
            'reviewableLoanId' => $reviewableLoanId,
        ]);
    }
}

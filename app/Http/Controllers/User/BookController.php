<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Book::class, 'book');
    }

    public function index(Request $request)
    {
        $books = Book::query()
            ->with(['publisher', 'authors'])
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($request->publisher, function ($query, $publisherId) {
                $query->where('publisher_id', $publisherId);
            })
            ->when($request->author, function ($query, $authorId) {
                $query->whereHas('authors', function ($q) use ($authorId) {
                    $q->where('authors.id', $authorId);
                });
            })
            ->when($request->sort, function ($query, $sort) {
                match ($sort) {
                    'preco_asc'  => $query->orderBy('price', 'asc'),
                    'preco_desc' => $query->orderBy('price', 'desc'),
                    'titulo_az'  => $query->orderBy('title', 'asc'),
                    'titulo_za'  => $query->orderBy('title', 'desc'),
                    default      => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(15, ['*'], 'pag')
            ->withQueryString();

        $filters = $request->only(['search', 'sort', 'publisher', 'author']);

        if (!in_array($filters['sort'] ?? null, ['preco_asc', 'preco_desc', 'titulo_az', 'titulo_za'], true)) {
            $filters['sort'] = '';
        }

        return Inertia::render('User/Books/Index', [
            'books' => $books,
            'filters' => $filters,
            'publishers' => Publisher::query()->orderBy('name')->limit(100)->get(),
            'authors' => Author::query()->orderBy('name')->limit(100)->get(),
        ]);
    }

    public function show(Book $book)
    {
        return Inertia::render('User/Books/Show', [
            'book' => $book->load('authors', 'publisher'),
            'loans' => $book->loans()->with('user')->latest()->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Author::class, 'author');
    }

    public function index(Request $request)
    {
        $authors = Author::query()
            ->when($request->search, function($query, $search){
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->withCount('books')
            ->when($request->sort, function($query, $sort){
                match ($sort) {
                    'nome_az'          => $query->orderBy('name', 'asc'),
                    'nome_za'          => $query->orderBy('name', 'desc'),
                    'livros_asc'       => $query->orderBy('books_count', 'asc'),
                    'livros_desc'      => $query->orderBy('books_count', 'desc'),
                    default            => $query->latest(),
                };
            }, function($query){
                $query->latest();
            })
            ->paginate(15, ['*'], 'pag')
            ->withQueryString();

        $filters = $request->only(['search', 'sort']);

        if (!in_array($filters['sort'] ?? null, ['nome_az', 'nome_za', 'livros_asc', 'livros_desc'], true)) {
            $filters['sort'] = '';
        }

        return Inertia::render('User/Authors/Index', [
            'authors' => $authors,
            'filters' => $filters,
        ]);
    }

    public function show(Author $author)
    {
        return Inertia::render('User/Authors/Show', [
            'author' => $author,
            'books' => $author->books()->with('publisher')->get()
        ]);
    }
}

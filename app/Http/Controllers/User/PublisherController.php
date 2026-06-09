<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublisherController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Publisher::class, 'publisher');
    }

    public function index(Request $request)
    {
        $publishers = Publisher::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->withCount('books')
            ->when($request->sort, function ($query, $sort) {
                match ($sort) {
                    'nome_az' => $query->orderBy('name', 'asc'),
                    'nome_za' => $query->orderBy('name', 'desc'),
                    'livros_asc' => $query->orderBy('books_count', 'asc'),
                    'livros_desc' => $query->orderBy('books_count', 'desc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(15, ['*'], 'pag')
            ->withQueryString();

        $filters = $request->only(['search', 'sort']);

        if (! in_array($filters['sort'] ?? null, ['nome_az', 'nome_za', 'livros_asc', 'livros_desc'], true)) {
            $filters['sort'] = '';
        }

        return Inertia::render('User/Publishers/Index', [
            'publishers' => $publishers,
            'filters' => $filters,
        ]);
    }

    public function show(Publisher $publisher)
    {
        return Inertia::render('User/Publishers/Show', [
            'publisher' => $publisher,
            'books' => $publisher->books()->get(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class BookCatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = $validated['search'] ?? '';

        if ($query === '') {
            $books = Book::query()
                ->with(['authors', 'category'])
                ->latest()
                ->paginate(12);
        } else {

            $books = Book::search($query)
                ->query(fn ($q) => $q->with(['authors', 'category']))
                ->paginate(12);
        }

        $books->appends(['search' => $query]);

        return Inertia::render('Public/Books/Index', [
            'books' => $books,
            'filters' => ['search' => $query],
        ]);
    }
}

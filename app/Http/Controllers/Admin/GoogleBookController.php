<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GoogleBooksExport;
use App\Http\Controllers\Controller;
use App\Services\GoogleBooksService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class GoogleBookController extends Controller
{
    public function index(Request $request, GoogleBooksService $googleBooks): Response
    {
        $search = $request->query('search', '');
        $apiQuery = trim($search) !== '' ? $search : 'subject:fiction';

        $paginatedExternalBooks = $googleBooks->search(
            query: $apiQuery,
            page: (int) $request->query('page', 1),
            perPage: 15,
            maxPages: 5,
            path: $request->url(),
            queryParams: $request->query(),
            apiParams: [
                'printType' => 'books',
                'orderBy' => 'relevance',
                'langRestrict' => 'pt',
            ]
        );

        return Inertia::render('Books/GoogleIndex', [
            'books' => $paginatedExternalBooks,
            'filters' => ['search' => $search],
        ]);
    }

    public function show($id, GoogleBooksService $googleBooks): RedirectResponse|Response
    {
        $book = $googleBooks->find($id);

        if ($book) {
            return Inertia::render('Books/GoogleShow', [
                'book' => $book,
                'loans' => [],
            ]);
        }

        return redirect()->route('livros.google-index')->with('error', 'Livro não encontrado na API da Google.');
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new GoogleBooksExport($request), 'livros_google.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao exportar os livros da Google.');
        }
    }
}

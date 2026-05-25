<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;
use App\Exports\GoogleBooksExport;
use Maatwebsite\Excel\Facades\Excel;

class GoogleBookController extends Controller
{
    public function index(Request $request): Response
    {

        $search = $request->query('search', '');
        $paginatedExternalBooks = null;

        $apiQuery = trim($search) !== '' ? $search : 'subject:fiction';

        $perPage = 15;
        $maxPages = 5;
        $page = (int) $request->query('page', 1);

        $startIndex = ($page - 1) * $perPage;

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $apiQuery,
            'maxResults' => $perPage,
            'startIndex' => $startIndex,
            'printType' => 'books',
            'orderBy' => 'relevance',
            'langRestrict' => 'pt',
            'key' => config('services.google.books_key'),
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $googleTotal = $data['totalItems'] ?? 0;
            $totalItems = min($googleTotal, $perPage * $maxPages);

            $externalBooks = collect($data['items'] ?? [])->map(function ($item) {

                $googlePublisher = $item['volumeInfo']['publisher'] ?? null;

                $localPublisherId = $googlePublisher
                    ? Publisher::where('name', 'like', trim($googlePublisher))->first()?->id
                    : null;

                return [
                    'google_id' => $item['id'] ?? null,
                    'title' => $item['volumeInfo']['title'] ?? 'Sem título',
                    'autores' => implode(', ', $item['volumeInfo']['authors'] ?? ['Autor Desconhecido']),
                    'publisher_id' => $localPublisherId,
                    'publisher_name' => $googlePublisher,
                    'image_path' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? null,
                    'isbn' => collect($item['volumeInfo']['industryIdentifiers'] ?? [])
                        ->firstWhere('type', 'ISBN_13')?->{'identifier'} ?? null,
                    'description' => $item['volumeInfo']['description'] ?? '',
                ];
            })->all();

            $paginatedExternalBooks = new LengthAwarePaginator(
                $externalBooks,
                $totalItems,
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        }

        return Inertia::render('Books/GoogleIndex', [
            'books' => $paginatedExternalBooks,
            'filters' => ['search' => $search],
        ]);
    }

    public function show($id): \Illuminate\Http\RedirectResponse|Response
    {
        $response = Http::get("https://www.googleapis.com/books/v1/volumes/{$id}", [
            'key' => config('services.google.books_key'),
        ]);

        if ($response->successful()) {
            $item = $response->json();
            
            $googlePublisher = $item['volumeInfo']['publisher'] ?? null;
            $localPublisherId = $googlePublisher
                ? Publisher::where('name', 'like', trim($googlePublisher))->first()?->id
                : null;

            $authors = collect($item['volumeInfo']['authors'] ?? ['Autor Desconhecido'])->map(function($author) {
                return ['id' => null, 'name' => $author];
            })->all();

            $book = [
                'id' => $item['id'],
                'google_id' => $item['id'],
                'title' => $item['volumeInfo']['title'] ?? 'Sem título',
                'authors' => $authors,
                'publisher' => ['id' => $localPublisherId, 'name' => $googlePublisher],
                'publisher_name' => $googlePublisher,
                'image_path' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? null,
                'isbn' => collect($item['volumeInfo']['industryIdentifiers'] ?? [])
                    ->firstWhere('type', 'ISBN_13')?->{'identifier'} ?? null,
                'bibliography' => $item['volumeInfo']['description'] ?? '',
                'price' => 0,
                'is_available' => false,
                'total_stock' => 1,
            ];

            return Inertia::render('Books/GoogleShow', [
                'book' => (object)$book,
                'loans' => []
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

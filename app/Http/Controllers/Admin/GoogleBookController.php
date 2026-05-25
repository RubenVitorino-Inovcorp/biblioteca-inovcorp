<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class GoogleBookController extends Controller
{
    public function __invoke(Request $request): Response
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
            'key' => config('services.google.books_key'),
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $googleTotal = $data['totalItems'] ?? 0;
            $totalItems = min($googleTotal, $perPage * $maxPages);
            $googlePublisher = null;
            $localPublisherId = null;

            $externalBooks = collect($data['items'] ?? [])->map(function ($item) {

                $googlePublisher = $item['volumeInfo']['publisher'] ?? null;

                $localPublisherId = $googlePublisher
                    ? Publisher::where('name', 'like', trim($googlePublisher))->first()?->id
                    : null;

                return [
                    'google_id' => $item['id'] ?? null,
                    'titulo' => $item['volumeInfo']['title'] ?? 'Sem título',
                    'title' => $item['volumeInfo']['title'] ?? 'Sem título',
                    'autores' => implode(', ', $item['volumeInfo']['authors'] ?? ['Autor Desconhecido']),
                    'publisher_id' => $localPublisherId,
                    'publisher_name' => $googlePublisher,
                    'capa' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? null,
                    'image_path' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? null,
                    'isbn' => collect($item['volumeInfo']['industryIdentifiers'] ?? [])
                        ->firstWhere('type', 'ISBN_13')['identifier'] ?? null,
                    'description' => $item['volumeInfo']['description'] ?? '',
                    'price' => 0,
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
}

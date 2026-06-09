<?php

namespace App\Services;

use App\Models\Publisher;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    /**
     * Search for books using the Google Books API.
     */
    public function search(
        string $query,
        int $page = 1,
        int $perPage = 15,
        int $maxPages = 5,
        string $path = '',
        array $queryParams = [],
        array $apiParams = []
    ): ?LengthAwarePaginator {
        if (trim($query) === '') {
            return null;
        }

        $startIndex = ($page - 1) * $perPage;

        $params = array_merge([
            'q' => $query,
            'maxResults' => $perPage,
            'startIndex' => $startIndex,
            'key' => config('services.google.books_key'),
        ], $apiParams);

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', $params);

        if ($response->successful()) {
            $data = $response->json();

            $googleTotal = $data['totalItems'] ?? 0;
            $totalItems = min($googleTotal, $perPage * $maxPages);

            $publisherNames = collect($data['items'] ?? [])
                ->pluck('volumeInfo.publisher')
                ->filter()
                ->map(fn ($name) => trim($name))
                ->unique()
                ->values();

            $publishers = Publisher::whereIn('name', $publisherNames)->get()->keyBy('name');

            $externalBooks = collect($data['items'] ?? [])->map(function ($item) use ($publishers) {
                $googlePublisher = $item['volumeInfo']['publisher'] ?? null;
                $localPublisherId = $googlePublisher
                    ? $publishers->get(trim($googlePublisher))?->id
                    : null;

                return [
                    'google_id' => $item['id'] ?? null,
                    'title' => $item['volumeInfo']['title'] ?? 'Sem título',
                    'titulo' => $item['volumeInfo']['title'] ?? 'Sem título',
                    'autores' => implode(', ', $item['volumeInfo']['authors'] ?? ['Autor Desconhecido']),
                    'publisher_id' => $localPublisherId,
                    'publisher_name' => $googlePublisher,
                    'capa' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? null,
                    'image_path' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? '/storage/imagens/default.webp',
                    'isbn' => collect($item['volumeInfo']['industryIdentifiers'] ?? [])
                        ->firstWhere('type', 'ISBN_13')?->{'identifier'} ?? null,
                    'description' => $item['volumeInfo']['description'] ?? '',
                ];
            })->all();

            return new LengthAwarePaginator(
                $externalBooks,
                $totalItems,
                $perPage,
                $page,
                [
                    'path' => $path,
                    'query' => $queryParams,
                ]
            );
        }

        return null;
    }

    /**
     * Find a single book by Google ID.
     */
    public function find(string $id): ?object
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

            $authors = collect($item['volumeInfo']['authors'] ?? ['Autor Desconhecido'])->map(function ($author) {
                return ['id' => null, 'name' => $author];
            })->all();

            $book = [
                'id' => $item['id'],
                'google_id' => $item['id'],
                'title' => $item['volumeInfo']['title'] ?? 'Sem título',
                'titulo' => $item['volumeInfo']['title'] ?? 'Sem título',
                'authors' => $authors,
                'autores' => implode(', ', $item['volumeInfo']['authors'] ?? ['Autor Desconhecido']),
                'publisher' => ['id' => $localPublisherId, 'name' => $googlePublisher],
                'publisher_id' => $localPublisherId,
                'publisher_name' => $googlePublisher,
                'image_path' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? '/storage/imagens/default.webp',
                'capa' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? '/storage/imagens/default.webp',
                'isbn' => collect($item['volumeInfo']['industryIdentifiers'] ?? [])
                    ->firstWhere('type', 'ISBN_13')?->{'identifier'} ?? null,
                'bibliography' => $item['volumeInfo']['description'] ?? '',
                'description' => $item['volumeInfo']['description'] ?? '',
                'price' => 0,
                'is_available' => false,
                'total_stock' => 1,
            ];

            return (object) $book;
        }

        return null;
    }
}

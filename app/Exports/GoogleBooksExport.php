<?php

namespace App\Exports;

use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GoogleBooksExport implements FromArray, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $search = $this->request->query('search', '');
        $apiQuery = trim($search) !== '' ? $search : 'subject:fiction';
        $maxResults = 40; // Max allowed by Google API per request

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $apiQuery,
            'maxResults' => $maxResults,
            'printType' => 'books',
            'orderBy' => 'relevance',
            'langRestrict' => 'pt',
            'key' => config('services.google.books_key'),
        ]);

        $exportData = [];

        if ($response->successful()) {
            $data = $response->json();
            $items = $data['items'] ?? [];

            foreach ($items as $item) {
                $volumeInfo = $item['volumeInfo'] ?? [];
                
                $isbn = collect($volumeInfo['industryIdentifiers'] ?? [])
                    ->firstWhere('type', 'ISBN_13')?->{'identifier'} ?? 'N/D';
                $title = $volumeInfo['title'] ?? 'Sem título';
                $authors = collect($volumeInfo['authors'] ?? ['Autor Desconhecido'])->implode(', ');
                $publisher = $volumeInfo['publisher'] ?? 'Desconhecida';
                $description = $volumeInfo['description'] ?? 'Sem descrição';

                $exportData[] = [
                    $isbn,
                    $title,
                    $publisher,
                    $authors,
                    $description,
                ];
            }
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'ISBN',
            'Título',
            'Editora',
            'Autores',
            'Descrição'
        ];
    }
}

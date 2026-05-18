<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;

class BooksExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        return Book::query()
            ->with(['authors', 'publisher'])
            ->when($this->request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($this->request->publisher, fn($query, $id) => $query->where('publisher_id', $id))
            ->when($this->request->author, function ($query, $authorId) {
                $query->whereHas('authors', function ($q) use ($authorId) {
                    $q->where('authors.id', $authorId);
                });
            })
            ->when($this->request->sort, function ($query, $sort) {
                if ($sort === 'preco_asc') {
                    return $query->orderBy('price', 'asc');
                } elseif ($sort === 'preco_desc') {
                    return $query->orderBy('price', 'desc');
                } elseif ($sort === 'titulo_az') {
                    return $query->orderBy('title', 'asc');
                } else {
                    return $query->latest();
                }
            }, function ($query) {
                return $query->latest();
            });
    }

    public function headings(): array
    {
        return [
            'ISBN',
            'Título',
            'Preço',
            'Bibliografia',
            'Editora',
            'Autores',
        ];
    }

    public function map($row): array
    {
        return [
            $row->isbn,
            $row->title,
            $row->price,
            $row->bibliography,
            $row->publisher?->name,
            $row->authors->pluck('name')->implode(', '),
        ];
    }
}

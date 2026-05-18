<?php

namespace App\Exports;

use App\Models\Author;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;

class AuthorsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        return Author::query()->when($this->request->search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
            ->withCount('books')
            ->when($this->request->sort, function ($query, $sort) {
                match ($sort) {
                    'nome_az' => $query->orderBy('name', 'asc'),
                    'nome_za' => $query->orderBy('name', 'desc'),
                    'livros_asc' => $query->orderBy('books_count', 'asc'),
                    'livros_desc' => $query->orderBy('books_count', 'desc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            });
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Total Livros',
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->books_count,
        ];
    }
}

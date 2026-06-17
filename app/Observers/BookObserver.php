<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Book;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Storage;

class BookObserver
{
    protected const FIELD_TRANSLATIONS = [
        'title' => 'Título',
        'bibliography' => 'Bibliografia',
        'price' => 'Preço',
        'image_path' => 'Capa',
        'total_stock' => 'Stock total',
        'available_stock' => 'Stock disponível',
        'author_id' => 'Autor',
        'isbn' => 'ISBN',
        'publisher_id' => 'Editora',
    ];

    /**
     * Handle the Book "created" event.
     */
    public function created(Book $book): void
    {
        ActivityLogger::log(
            module: 'books',
            objectId: (string) $book->id,
            action: "Criação do livro: #{$book->id} - {$book->title}",
        );
    }

    /**
     * Handle the Book "updated" event.
     */
    public function updated(Book $book): void
    {
        if ($book->wasChanged(['available_stock', 'total_stock']) && ! $book->wasChanged(['title', 'price', 'isbn', 'bibliography', 'image_path'])) {
            return;
        }

        $fields = array_map(fn (string $field): string => self::FIELD_TRANSLATIONS[$field] ?? $field, array_keys($book->getChanges()));

        $cleanFields = array_diff($fields, ['updated_at']);

        if (empty($cleanFields)) {
            return;
        }

        ActivityLogger::log(
            module: 'books',
            objectId: (string) $book->id,
            action: 'Atualização de '.implode(', ', $cleanFields)." do livro: #{$book->id} - {$book->title}",
        );
    }

    /**
     * Handle the Book "deleted" event.
     */
    public function deleted(Book $book): void
    {
        ActivityLogger::log(
            module: 'books',
            objectId: (string) $book->id,
            action: "Eliminação do livro: #{$book->id} - {$book->title}",
        );
    }

    /**
     * Remove o ficheiro físico antes do registo ser apagado da BD.
     */
    public function deleting(Book $book): void
    {
        if ($book->image_path && ! str_starts_with($book->image_path, 'http') && str_starts_with($book->image_path, '/storage/')) {
            $path = substr($book->image_path, 9);

            if (! str_contains($path, '..')) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}

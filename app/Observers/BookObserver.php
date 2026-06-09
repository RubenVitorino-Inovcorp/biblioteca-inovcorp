<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Book;
use Illuminate\Support\Facades\Storage;

class BookObserver
{
    /**
     * Remove o ficheiro físico antes do registo ser apagado da BD.
     */
    public function deleting(Book $book): void
    {
        if ($book->image_path && ! str_starts_with($book->image_path, 'http') && str_starts_with($book->image_path, '/storage/')) {
            $path = substr($book->image_path, 9); // Remove '/storage/' prefix (9 characters)
            // Ensure the path doesn't contain directory traversal sequences
            if (! str_contains($path, '..')) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}

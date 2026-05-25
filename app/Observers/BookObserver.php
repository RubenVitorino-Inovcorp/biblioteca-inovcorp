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
        if ($book->image_path && !str_starts_with($book->image_path, 'http')) {
            $path = str_replace('/storage/', '', $book->image_path);
            Storage::disk('public')->delete($path);
        }
    }
}


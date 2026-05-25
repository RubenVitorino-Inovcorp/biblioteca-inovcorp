<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Author;
use Illuminate\Support\Facades\Storage;

class AuthorObserver
{
    /**
     * Remove o ficheiro físico antes do registo ser apagado da BD.
     */
    public function deleting(Author $author): void
    {
        if ($author->photo_path && !str_starts_with($author->photo_path, 'http')) {
            $path = str_replace('/storage/', '', $author->photo_path);
            Storage::disk('public')->delete($path);
        }
    }
}


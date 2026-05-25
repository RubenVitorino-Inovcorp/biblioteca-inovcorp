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
        if ($author->photo_path && !str_starts_with($author->photo_path, 'http') && str_starts_with($author->photo_path, '/storage/')) {
            $path = substr($author->photo_path, 9);
            Storage::disk('public')->delete($path);
        }
    }
}


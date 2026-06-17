<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Author;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Storage;

class AuthorObserver
{
    public function created(Author $author): void
    {
        ActivityLogger::log(
            module: 'authors',
            objectId: (string) $author->id,
            action: "Criação do autor: #{$author->id} - {$author->name}",
        );
    }

    public function updated(Author $author): void
    {
        ActivityLogger::log(
            module: 'authors',
            objectId: (string) $author->id,
            action: "Atualização do autor: #{$author->id} - {$author->name}",
        );
    }

    public function deleted(Author $author): void
    {
        ActivityLogger::log(
            module: 'authors',
            objectId: (string) $author->id,
            action: "Eliminação do autor: #{$author->id} - {$author->name}",
        );
    }

    /**
     * Remove o ficheiro físico antes do registo ser apagado da BD.
     */
    public function deleting(Author $author): void
    {
        if ($author->photo_path && ! str_starts_with($author->photo_path, 'http') && str_starts_with($author->photo_path, '/storage/')) {
            $path = substr($author->photo_path, 9);

            if (! str_contains($path, '..')) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}

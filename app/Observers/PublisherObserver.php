<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Publisher;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Storage;

class PublisherObserver
{
    public function created(Publisher $publisher): void
    {
        ActivityLogger::log(
            module: 'publishers',
            objectId: (string) $publisher->id,
            action: "Criação da editora: #{$publisher->id} - {$publisher->name}",
        );
    }

    public function updated(Publisher $publisher): void
    {
        ActivityLogger::log(
            module: 'publishers',
            objectId: (string) $publisher->id,
            action: "Atualização da editora: #{$publisher->id} - {$publisher->name}",
        );
    }

    public function deleted(Publisher $publisher): void
    {
        ActivityLogger::log(
            module: 'publishers',
            objectId: (string) $publisher->id,
            action: "Eliminação da editora: #{$publisher->id} - {$publisher->name}",
        );
    }

    /**
     * Remove o ficheiro físico antes do registo ser apagado da BD.
     */
    public function deleting(Publisher $publisher): void
    {
        if ($publisher->logo_path && ! str_starts_with($publisher->logo_path, 'http')) {
            $path = str_replace('/storage/', '', $publisher->logo_path);
            Storage::disk('public')->delete($path);
        }
    }
}

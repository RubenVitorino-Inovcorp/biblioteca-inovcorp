<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Publisher;
use Illuminate\Support\Facades\Storage;

class PublisherObserver
{
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

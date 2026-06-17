<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Review;
use App\Services\ActivityLogger;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        ActivityLogger::log(
            module: 'reviews',
            objectId: (string) $review->id,
            action: "Criação da review: #{$review->id} - ".($review->book ? $review->book->title : 'Desconhecido'),
        );
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        if (! $review->wasChanged(['rating', 'comment']) || $review->wasChanged(['user_id', 'book_id'])) {
            return;
        }

        $fields = array_keys($review->getChanges());

        $cleanFields = array_diff($fields, ['updated_at']);

        if (empty($cleanFields)) {
            return;
        }

        ActivityLogger::log(
            module: 'reviews',
            objectId: (string) $review->id,
            action: "Atualização da review: #{$review->id} - ".($review->book ? $review->book->title : 'Desconhecido'),
        );
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        ActivityLogger::log(
            module: 'reviews',
            objectId: (string) $review->id,
            action: "Eliminação da review: #{$review->id} - ".($review->book ? $review->book->title : 'Desconhecido'),
        );
    }
}

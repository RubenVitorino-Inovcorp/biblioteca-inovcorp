<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\ActivityLogger;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        ActivityLogger::log(
            module: 'orders',
            objectId: (string) $order->id,
            action: "Criação da encomenda: #{$order->id}",
        );
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            ActivityLogger::log(
                module: 'orders',
                objectId: (string) $order->id,
                action: "Atualização da encomenda #{$order->id}",
            );
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}

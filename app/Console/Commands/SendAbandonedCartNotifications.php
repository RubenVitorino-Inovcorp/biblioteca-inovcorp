<?php

namespace App\Console\Commands;

use App\Enums\CartStatus;
use App\Mail\AbandonedCartMail;
use App\Models\Cart;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

#[Signature('carts:send-abandoned-cart-notifications')]
#[Description('Send an email to users who abandoned their cart')]
class SendAbandonedCartNotifications extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeLimit = now()->subHour();

        Cart::where('status', CartStatus::ACTIVE->value)
            ->where('updated_at', '<', $timeLimit)
            ->where('has_notified', false)
            ->with(['user', 'items.book'])
            ->chunkById(100, function (Collection $carts): void { // chunkById para nao carregar tudo de uma vez
                foreach ($carts as $cart) {
                    $this->processCart($cart);
                }
            });

        return self::SUCCESS;
    }

    private function processCart(Cart $cart): void
    {
        if (is_null($cart->user) || $cart->items->isEmpty()) {
            return;
        }

        // Envio do email e set de notificado para evitar enviar emails duplicados
        Mail::to($cart->user->email)->queue(new AbandonedCartMail($cart));
        $cart->update(['has_notified' => true]);
    }
}

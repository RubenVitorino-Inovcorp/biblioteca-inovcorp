<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class CheckoutController extends Controller
{
    /**
     * Criar sessão de pagamento no Stripe para uma encomenda pendente.
     * A segurança PCI-DSS é da responsabilidade da Stripe e é gerado um link de pagamento seguro.
     */
    public function __invoke(Order $order): Response
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Validar se a encomenda já não foi paga
        if ($order->status !== OrderStatus::PENDING) {
            return redirect()->route('encomendas.index')->with('error', 'Esta encomenda já não está pendente.');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $lineItems = $this->buildLineItems($order);

            $session = $stripe->checkout->sessions->create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('catalog.checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('catalog.checkout.cancel'),
                'metadata' => ['order_id' => $order->id],
            ]);

            // Persistir o ID da sessão para o Webhook validar mais tarde
            $order->update(['stripe_session_id' => $session->id]);

            // Redirecionar para o checkout externo do Stripe
            return Inertia::location($session->url);

        } catch (ApiErrorException $e) {
            return redirect()->back()->with('error', 'Falha na comunicação com o operador de pagamento.');
        }
    }

    /**
     * Transformar itens da encomenda no formato estrito do SDK do Stripe.
     */
    private function buildLineItems(Order $order): array
    {
        $lineItems = [];
        foreach ($order->items as $item) {
            if (empty($item->book->title)) {
                throw new \InvalidArgumentException("Book title is missing for order item {$item->id}");
            }
            if ($item->price === null || $item->price <= 0) {
                throw new \InvalidArgumentException("Invalid price for order item {$item->id}");
            }
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item->book->title,
                    ],
                    'unit_amount' => (int) ($item->price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        return $lineItems;
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('catalog.carrinho.index')
            ->with('error', 'O pagamento foi cancelado ou não foi possível autorizar o cartão. Tente novamente.');
    }

    public function success(): RedirectResponse
    {
        return redirect()->route('encomendas.index')
            ->with('success', 'O pagamento foi concluído com sucesso! Os detalhes da encomenda serão enviados para o seu email.');
    }
}

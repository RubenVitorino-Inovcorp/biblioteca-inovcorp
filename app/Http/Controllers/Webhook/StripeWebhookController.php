<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhook;

use App\Enums\CartStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

final class StripeWebhookController extends Controller
{
    /**
     * Escutar e processar os eventos enviados pela Stripe.
     * Validar a assinatura digital para impedir payloads falsos.
     */
    public function __invoke(Request $request): HttpResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature') ?? '';
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (UnexpectedValueException $e) {
            Log::error('Stripe Webhook: Payload inválido.', ['error' => $e->getMessage()]);

            return response('Payload Inválido', HttpResponse::HTTP_BAD_REQUEST);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook: Assinatura digital inválida.', ['error' => $e->getMessage()]);

            return response('Assinatura Inválida', HttpResponse::HTTP_BAD_REQUEST);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            try {
                DB::transaction(function () use ($session): void {
                    // Lock para escrita para evitar que concorrência altere a mesma ordem simultaneamente
                    $order = Order::where('stripe_session_id', $session->id)
                        ->lockForUpdate()
                        ->first();

                    if ($order && $order->status === OrderStatus::PENDING) {
                        $order->update(['status' => OrderStatus::PAID]);
                        $order->reduceItemsStock();

                        // Converter o carrinho ativo do utilizador
                        $user = $order->user;
                        if ($user) {
                            $cart = Cart::where('user_id', $user->id)
                                ->where('status', CartStatus::ACTIVE->value)
                                ->first();

                            if ($cart) {
                                $cart->update(['status' => CartStatus::CONVERTED]);
                            }
                        }

                        Log::info("Encomenda nº {$order->order_number} paga e stock atualizado com sucesso.");
                    }
                });
            } catch (\RuntimeException $e) {
                Log::error('Stripe Webhook: Stock insuficiente.', [
                    'session_id' => $session->id,
                    'error' => $e->getMessage(),
                ]);

                $order = Order::where('stripe_session_id', $session->id)->first();
                if ($order) {
                    $order->update(['status' => OrderStatus::CANCELLED]);
                }

                return response('Stock insuficiente', HttpResponse::HTTP_OK);
            } catch (Exception $e) {
                Log::error('Stripe Webhook: Erro ao processar transação de pagamento.', [
                    'session_id' => $session->id,
                    'error' => $e->getMessage(),
                ]);

                // Devolve 500 para a Stripe tentar enviar novamente mais tarde
                return response('Erro Interno', HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return response('Webhook Processado', HttpResponse::HTTP_OK);
    }
}

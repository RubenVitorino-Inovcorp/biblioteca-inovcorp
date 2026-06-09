<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Enums\CartStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class OrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->with(['items.book', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('items.book', function ($bookQuery) use ($search) {
                            $bookQuery->where('title', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->sort, function ($query, $sort) {
                match ($sort) {
                    'data_recente' => $query->orderBy('created_at', 'desc'),
                    'data_antiga' => $query->orderBy('created_at', 'asc'),
                    'numero_asc' => $query->orderBy('order_number', 'asc'),
                    'numero_desc' => $query->orderBy('order_number', 'desc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(15, ['*'], 'pag')
            ->withQueryString();

        $filters = $request->only(['search', 'sort', 'status']);

        if (! in_array($filters['sort'] ?? null, ['data_recente', 'data_antiga', 'numero_asc', 'numero_desc'], true)) {
            $filters['sort'] = '';
        }

        return Inertia::render('User/Orders/Index', [
            'orders' => $orders,
            'filters' => $filters,
            'active_orders' => Order::query()
                ->where('user_id', $user->id)
                ->where('status', OrderStatus::PAID)
                ->with(['items.book', 'user'])
                ->latest()
                ->get(),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['items.book', 'user']);

        return Inertia::render('User/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Transformar o carrinho ativo numa encomenda pendente.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $userId = (int) Auth::id();

        $validated = $request->validated();

        $cart = Cart::where('user_id', $userId)
            ->where('status', CartStatus::ACTIVE->value)
            ->with('items.book')
            ->first();

        if (is_null($cart) || $cart->items->isEmpty()) {
            return redirect()->back()->with('error', 'O teu carrinho está vazio.');
        }

        foreach ($cart->items as $item) {
            if (empty($item->book->title)) {
                return redirect()->back()->with('error', 'Não foi possível processar a encomenda. Um ou mais livros estão indisponíveis (título em falta).');
            }
        }

        // Garantir a integridade dos dados se houver uma falha na gravação dos itens
        $order = DB::transaction(function () use ($userId, $cart, $validated): Order {
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => 'ENC-'.strtoupper(bin2hex(random_bytes(4))),
                'status' => OrderStatus::PENDING,
                'total_price' => 0.0,
                'delivery_address' => $validated['street'].', '.$validated['city'].', '.$validated['postal_code'],
            ]);

            $total = 0.0;

            foreach ($cart->items as $item) {
                $unitPrice = (float) ($item->book->price ?? 0.0);

                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book_id,
                    'quantity' => $item->quantity,
                    'price' => $unitPrice,
                ]);

                $total += ($unitPrice * $item->quantity);
            }

            $order->update(['total_price' => $total]);

            return $order;
        });

        // Redirecionar para a rota que aponta para o CheckoutController (__invoke)
        return redirect()->route('catalog.checkout.process', ['order' => $order->id]);
    }
}

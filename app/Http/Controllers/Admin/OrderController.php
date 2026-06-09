<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['items.book', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
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

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items.book', 'user']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }
}

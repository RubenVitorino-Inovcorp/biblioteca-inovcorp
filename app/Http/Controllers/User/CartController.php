<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Enums\CartStatus;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = $this->getOrCreateCart((int) Auth::id());

        $cart->load(['items.book.publisher', 'items.book.authors']);

        return Inertia::render('User/Cart/Index', [
            'cart' => $cart,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ];

        $validated = $request->validate($rules);

        $cart = $this->getOrCreateCart((int) Auth::id());

        $cartItem = $cart->items()->where('book_id', $validated['book_id'])->first();

        $book = Book::findOrFail($validated['book_id']);
        $cartItemCount = $cartItem ? $cartItem->quantity : 0;

        if ($book->available_stock <= 0) {
            return redirect()->back()->withErrors(['message' => 'O livro não está em stock!']);
        }

        if (($cartItemCount + $validated['quantity']) > $book->available_stock) {
            return redirect()->back()->withErrors(['message' => 'Não há stock suficiente para adicionar ao carrinho!']);
        }

        if (is_null($cartItem)) {
            CartItem::create([
                'cart_id' => $cart->id,
                'book_id' => $validated['book_id'],
                'quantity' => $validated['quantity'],
            ]);
        } else {
            $cartItem->increment('quantity', (int) $validated['quantity']);
        }

        $cart->has_notified = false;
        $cart->touch();

        return redirect()->back()->with('success', 'Livro adicionado ao carrinho com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {

        $cart = $this->getOrCreateCart((int) Auth::id());
        $cart->items()->delete();

        $cart->touch();

        return redirect()->route('catalog.carrinho.index')->with('success', 'Carrinho foi limpo com sucesso!');
    }

    private function getOrCreateCart(int $userId): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $userId, 'status' => CartStatus::ACTIVE->value],
            ['cart_number' => 'CRT-'.strtoupper(bin2hex(random_bytes(4)))]
        );
    }
}

<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Client\Interfaces\CartRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartRepository implements CartRepositoryInterface
{
    public function getCartForUserOrSession(Request $request)
    {
        if (Auth::check()) {
            return Cart::with('items.product','items.variant')->firstOrCreate(['user_id' => Auth::id()]);
        }

        return Cart::with('items.product','items.variant')->firstOrCreate(['session_id' => $request->session()->getId()]);
    }

    public function addItem($cart, array $data)
    {
        $existing = $cart->items()->where('product_id', $data['product_id'])->where('variant_id', $data['variant_id'] ?? null)->first();
        if ($existing) {
            $existing->quantity = max(1, $existing->quantity + ($data['quantity'] ?? 1));
            $existing->unit_price = $data['unit_price'] ?? $existing->unit_price;
            $existing->save();
            return $existing;
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $data['product_id'],
            'variant_id' => $data['variant_id'] ?? null,
            'quantity' => $data['quantity'] ?? 1,
            'unit_price' => $data['unit_price'] ?? null,
        ]);
    }

    public function clearCart($cart)
    {
        if (!$cart) return;
        $cart->items()->delete();
        $cart->delete();
    }

    public function updateItem($cart, int $itemId, array $data)
    {
        $item = $cart->items()->where('id', $itemId)->first();
        if (!$item) return null;

        if (isset($data['quantity'])) {
            $item->quantity = max(1, (int) $data['quantity']);
        }
        if (isset($data['unit_price'])) {
            $item->unit_price = $data['unit_price'];
        }

        $item->save();
        return $item;
    }

    public function removeItem($cart, int $itemId)
    {
        $item = $cart->items()->where('id', $itemId)->first();
        if (!$item) return false;
        $item->delete();
        return true;
    }
}

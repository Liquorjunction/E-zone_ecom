<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Orders\OrderCheckoutRequest;
use App\Models\Cart;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function checkout(OrderCheckoutRequest $request, OrderService $orders)
    {
        if (Auth::check()) {
            $cart = Cart::with('items')->where('user_id', Auth::id())->first();
        } else {
            $cart = Cart::with('items')->where('session_id', $request->session()->getId())->first();
        }

        if (!$cart || $cart->items->isEmpty()) {
            return back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        $order = $orders->createFromCart($cart, $request->validated());

        Log::info('Client order created', ['order_id' => $order->id ?? null, 'user' => Auth::id()]);

        return redirect()->route('checkout-success')->with('order_id', $order->id ?? null);
    }

    /**
     * Show the checkout page (form).
     */
    public function show(Request $request, CartService $cartService)
    {
        $cart = $cartService->getCart($request);

        // If cart is empty, redirect back to cart page
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $step = (int) $request->get('step', 1);
        return view('client.pages.checkout-process', [
            'cart' => $cart,
            'step' => $step,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Cart\CartAddRequest;
use App\Services\CartService;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class CartController extends Controller
{
    public function add(CartAddRequest $request, CartService $cartService, LoggerInterface $logger)
    {
        $data = $request->validated();
        $item = $cartService->addItem($request, $data);
        $logger->info('Added item to cart via controller', ['item_id' => $item->id ?? null]);

        // If request expects JSON (AJAX), return JSON response
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Item added to cart.', 'item_id' => $item->id ?? null]);
        }

        // If the user clicked "Buy Now" (submit button with name=buy_now), redirect to checkout flow
        if ($request->filled('buy_now')) {
            return redirect()->route('checkout', ['step' => 2])->with('success', 'Item added. Proceed to checkout.');
        }

        // Default: redirect to cart page (production friendly)
        return redirect()->route('cart')->with('success', 'Item added to cart.');
    }

    public function show(Request $request, CartService $cartService, LoggerInterface $logger)
    {
        $cart = $cartService->getCart($request);
        $logger->info('Showing cart', ['cart_id' => $cart->id ?? null]);
        $step = (int) $request->get('step', 1);
        return view('client.pages.checkout-process', compact('cart', 'step'));
    }

    public function update(Request $request, CartService $cartService, LoggerInterface $logger)
    {
        $itemId = (int) $request->input('item_id');
        $data = $request->only(['quantity']);
        $item = $cartService->updateItem($request, $itemId, $data);
        $logger->info('Updated cart item via controller', ['item_id' => $item->id ?? null]);

        // Prepare updated cart summary
        $cart = $cartService->getCart($request);
        $subtotal = $cart ? $cart->items->sum(function($i){ return ($i->quantity * $i->unit_price); }) : 0;
        $tax = round($subtotal * 0.08, 2);
        $shipping = 0.00;
        $total = $subtotal + $tax + $shipping;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'item' => [
                    'id' => $item->id ?? null,
                    'quantity' => $item->quantity ?? null,
                    'unit_price' => $item->unit_price ?? null,
                    'line_total' => isset($item->quantity, $item->unit_price) ? ($item->quantity * $item->unit_price) : null,
                ],
                'summary' => [
                    // numeric values (raw) for accurate JS math
                    'subtotal' => round($subtotal, 2),
                    'tax' => round($tax, 2),
                    'shipping' => round($shipping, 2),
                    'total' => round($total, 2),
                    'itemCount' => $cart ? (int) $cart->items->sum('quantity') : 0,
                ],
            ]);
        }

        return redirect()->route('cart')->with('success', 'Cart updated.');
    }

    public function remove(Request $request, CartService $cartService, LoggerInterface $logger)
    {
        $itemId = (int) $request->input('item_id');
        $ok = $cartService->removeItem($request, $itemId);
        $logger->info('Removed cart item via controller', ['item_id' => $itemId, 'success' => $ok]);

        $cart = $cartService->getCart($request);
        $subtotal = $cart ? $cart->items->sum(function($i){ return ($i->quantity * $i->unit_price); }) : 0;
        $tax = round($subtotal * 0.08, 2);
        $shipping = 0.00;
        $total = $subtotal + $tax + $shipping;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => (bool) $ok,
                'message' => $ok ? 'Item removed from cart.' : 'Failed to remove item.',
                'summary' => [
                    'subtotal' => round($subtotal, 2),
                    'tax' => round($tax, 2),
                    'shipping' => round($shipping, 2),
                    'total' => round($total, 2),
                    'itemCount' => $cart ? (int) $cart->items->sum('quantity') : 0,
                ],
            ]);
        }

        return redirect()->route('cart')->with('success', 'Item removed from cart.');
    }
}

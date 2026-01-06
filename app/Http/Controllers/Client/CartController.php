<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Cart\CartAddRequest;
use App\Http\Requests\Client\Coupon\ApplyCouponRequest;
use App\Services\CartService;
use App\Services\CouponService;
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
            // return updated cart summary including item count
            $cart = $cartService->getCart($request);
            $itemCount = $cart ? (int) $cart->items->sum('quantity') : 0;
            return response()->json([
                'success' => true,
                'message' => 'Item added to cart.',
                'item_id' => $item->id ?? null,
                'summary' => [
                    'itemCount' => $itemCount,
                ],
            ]);
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

        // Calculate totals with discounts
        $totals = $cart ? $cart->calculateTotals() : [
            'subtotal' => 0,
            'total_discount' => 0,
            'total' => 0,
            'items' => []
        ];

        return view('client.pages.checkout-process', compact('cart', 'step', 'totals'));
    }

    public function update(Request $request, CartService $cartService, LoggerInterface $logger)
    {
        $itemId = (int) $request->input('item_id');
        $data = $request->only(['quantity']);
        $item = $cartService->updateItem($request, $itemId, $data);
        $logger->info('Updated cart item via controller', ['item_id' => $item->id ?? null]);

        // Prepare updated cart summary with discounts
        $cart = $cartService->getCart($request);
        $totals = $cart ? $cart->calculateTotals() : [
            'subtotal' => 0,
            'total_discount' => 0,
            'total' => 0,
            'items' => []
        ];

        // Calculate tax and shipping
        $tax = round($totals['total'] * 0.08, 2);
        $shipping = 0.00;
        $finalTotal = $totals['total'] + $tax + $shipping;

        // Get discount info for this specific item
        $itemDiscount = null;
        if ($item && isset($totals['items'][$item->id])) {
            $itemInfo = $totals['items'][$item->id];
            $itemDiscount = [
                'has_discount' => $itemInfo['discount_amount'] > 0,
                'discount_amount' => $itemInfo['discount_amount'],
                'line_total_after_discount' => $itemInfo['line_total_after_discount'],
            ];
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'item' => [
                    'id' => $item->id ?? null,
                    'quantity' => $item->quantity ?? null,
                    'unit_price' => $item->unit_price ?? null,
                    'line_total' => isset($item->quantity, $item->unit_price) ? ($item->quantity * $item->unit_price) : null,
                    'discount' => $itemDiscount,
                ],
                'summary' => [
                    // numeric values (raw) for accurate JS math
                    'subtotal' => round($totals['subtotal'], 2),
                    'total_discount' => round($totals['total_discount'], 2),
                    'subtotal_after_discount' => round($totals['total'], 2),
                    'tax' => round($tax, 2),
                    'shipping' => round($shipping, 2),
                    'total' => round($finalTotal, 2),
                    'itemCount' => $cart ? (int) $cart->items->sum('quantity') : 0,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, CartService $cartService, LoggerInterface $logger)
    {
        // FIX: Check for 'id' (sent by JS) OR 'item_id'
        $itemId = (int) ($request->input('id') ?? $request->input('item_id'));

        if (!$itemId) {
            return response()->json(['success' => false, 'message' => 'Item ID is missing.']);
        }

        $ok = $cartService->removeItem($request, $itemId);
        $logger->info('Removed cart item via controller', ['item_id' => $itemId, 'success' => $ok]);

        // Recalculate totals using stored discount data
        $cart = $cartService->getCart($request);
        $totals = $cart ? $cart->calculateTotals() : [
            'subtotal' => 0,
            'total_discount' => 0,
            'total' => 0,
            'items' => []
        ];

        $tax = round($totals['total'] * 0.08, 2);
        $shipping = 0.00;
        $finalTotal = $totals['total'] + $tax + $shipping;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => (bool) $ok,
                'message' => $ok ? 'Item removed from cart.' : 'Failed to remove item.',
                'summary' => [
                    'subtotal' => round($totals['subtotal'], 2),
                    'total_discount' => round($totals['total_discount'], 2),
                    'subtotal_after_discount' => round($totals['total'], 2),
                    'tax' => round($tax, 2),
                    'shipping' => round($shipping, 2),
                    'total' => round($finalTotal, 2),
                    'itemCount' => $cart ? (int) $cart->items->sum('quantity') : 0,
                ],
            ]);
        }

        return redirect()->route('cart')->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(ApplyCouponRequest $request, CartService $cartService, CouponService $couponService, LoggerInterface $logger)
    {
        $cart = $cartService->getCart($request);

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
            ]);
        }

        $code = strtoupper($request->input('code'));
        $result = $couponService->validateAndApplyCoupon($code, $cart);

        $logger->info('Coupon application attempt', [
            'cart_id' => $cart->id,
            'code' => $code,
            'success' => $result['success'],
        ]);

        if ($result['success']) {
            // Recalculate totals with coupon
            $totals = $cart->calculateTotals();
            $subtotalAfterItemDiscounts = $totals['total'];
            $couponDiscount = $cart->coupon_discount_amount;
            $totalAfterCoupon = max(0, $subtotalAfterItemDiscounts - $couponDiscount);
            $tax = round($totalAfterCoupon * 0.08, 2);
            $shipping = 0.00;
            $finalTotal = $totalAfterCoupon + $tax + $shipping;

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'coupon' => $result['coupon'],
                'summary' => [
                    'subtotal' => round($totals['subtotal'], 2),
                    'item_discount' => round($totals['total_discount'], 2),
                    'subtotal_after_item_discount' => round($subtotalAfterItemDiscounts, 2),
                    'coupon_discount' => round($couponDiscount, 2),
                    'total_after_coupon' => round($totalAfterCoupon, 2),
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'total' => round($finalTotal, 2),
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ]);
    }

    public function removeCoupon(Request $request, CartService $cartService, CouponService $couponService, LoggerInterface $logger)
    {
        $cart = $cartService->getCart($request);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found.',
            ]);
        }

        $removed = $couponService->removeCoupon($cart);
        $logger->info('Coupon removed from cart', ['cart_id' => $cart->id, 'success' => $removed]);

        // Recalculate totals without coupon
        $totals = $cart->calculateTotals();
        $tax = round($totals['total'] * 0.08, 2);
        $shipping = 0.00;
        $finalTotal = $totals['total'] + $tax + $shipping;

        return response()->json([
            'success' => $removed,
            'message' => $removed ? 'Coupon removed.' : 'Failed to remove coupon.',
            'summary' => [
                'subtotal' => round($totals['subtotal'], 2),
                'item_discount' => round($totals['total_discount'], 2),
                'subtotal_after_item_discount' => round($totals['total'], 2),
                'coupon_discount' => 0,
                'total_after_coupon' => round($totals['total'], 2),
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => round($finalTotal, 2),
            ],
        ]);
    }
}

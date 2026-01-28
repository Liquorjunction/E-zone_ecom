<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Cart\CartAddRequest;
use App\Http\Requests\Client\Coupon\ApplyCouponRequest;
use App\Models\Address;
use App\Repositories\Client\Interfaces\CartRepositoryInterface as CartRepo;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\PriceCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class CartController extends Controller
{
    public function add(CartAddRequest $request, CartService $cartService, LoggerInterface $logger)
    {
        $data = $request->validated();
        try {
            $item = $cartService->addItem($request, $data);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
            return redirect()->route('cart')->with('error', $e->getMessage());
        }
        $logger->info('Added item to cart via controller', ['item_id' => $item->id ?? null]);

        if ($request->wantsJson() || $request->ajax()) {
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

        if ($request->filled('buy_now')) {
            return redirect()->route('checkout', ['step' => 2])->with('success', 'Item added. Proceed to checkout.');
        }

        return redirect()->route('cart')->with('success', 'Item added to cart.');
    }

    public function show(Request $request, CartService $cartService, LoggerInterface $logger, CartRepo $cartRepo, PriceCalculatorService $priceService)
    {
        $cart = $cartService->getCart($request);
        $logger->info('Showing cart', ['cart_id' => $cart->id ?? null]);
        $step = (int) $request->get('step', 1);
        Log::debug('CartController@show step value', ['step' => $step, 'request_step' => $request->get('step')]);

        $totals = $cart ? $priceService->calculate($cart) : [
            'subtotal' => 0,
            'total_item_discount' => 0,
            'subtotal_after_item_discount' => 0,
            'coupon_discount' => 0,
            'total_after_coupon' => 0,
            'tax_rate' => config('tax.rate', 20.0),
            'tax_amount' => 0,
            'taxable_amount' => 0,
            'shipping_method' => 'standard',
            'shipping_cost' => 0,
            'final_total' => 0,
            'items' => []
        ];

        $addresses = collect();
        if (Auth::check()) {
            $addresses = Address::where('user_id', Auth::id())->get();
        }

        return view('client.pages.checkout-process', compact('cart', 'step', 'totals', 'addresses'));
    }

    public function update(Request $request, CartService $cartService, LoggerInterface $logger, CartRepo $cartRepo, \App\Services\PriceCalculatorService $priceService)
    {
        $itemId = (int) $request->input('item_id');
        $data = $request->only(['quantity']);

        try {
            $item = $cartService->updateItem($request, $itemId, $data);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }

        $logger->info('Updated cart item via controller', ['item_id' => $item->id ?? null]);

        $cart = $cartService->getCart($request);
        $shippingMethod = $request->input('shipping_method', 'standard');
        $totals = $cart ? $priceService->calculate($cart, $shippingMethod) : [
            'subtotal' => 0,
            'total_item_discount' => 0,
            'subtotal_after_item_discount' => 0,
            'coupon_discount' => 0,
            'total_after_coupon' => 0,
            'tax_rate' => config('tax.rate', 20.0),
            'tax_amount' => 0,
            'taxable_amount' => 0,
            'shipping_method' => $shippingMethod,
            'shipping_cost' => 0,
            'final_total' => 0,
            'items' => []
        ];

        $itemDiscount = null;
        if ($item && isset($totals['items'][$item->id])) {
            $itemInfo = $totals['items'][$item->id];
            $itemDiscount = [
                'has_discount' => $itemInfo['discount_amount'] > 0,
                'discount_amount' => $itemInfo['discount_amount'],
                'line_total_after_discount' => $itemInfo['line_total_after_discount'] ?? null,
            ];
        }

        if ($request->wantsJson() || $request->ajax()) {
            $items = [];
            if ($cart) {
                foreach ($cart->items as $cartItem) {
                    $items[$cartItem->id] = [
                        'id' => $cartItem->id,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $cartItem->unit_price,
                        'final_price' => $cartItem->final_price ?? $cartItem->unit_price,
                        'discount_amount' => $cartItem->discount_amount ?? 0,
                    ];
                }
            }

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
                    'subtotal' => round($totals['subtotal'] ?? 0, 2),
                    'total_discount' => round($totals['total_item_discount'] ?? 0, 2),
                    'subtotal_after_discount' => round($totals['subtotal_after_item_discount'] ?? 0, 2),
                    'coupon_discount' => round($totals['coupon_discount'] ?? 0, 2),
                    'tax' => round($totals['tax_amount'] ?? 0, 2),
                    'shipping' => round($totals['shipping_cost'] ?? 0, 2),
                    'total' => round($totals['final_total'] ?? 0, 2),
                    'itemCount' => $cart ? (int) $cart->items->sum('quantity') : 0,
                    'items' => $items,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, CartService $cartService, LoggerInterface $logger, PriceCalculatorService $priceService)
    {
        $itemId = (int) ($request->input('id') ?? $request->input('item_id'));

        if (!$itemId) {
            return response()->json(['success' => false, 'message' => 'Item ID is missing.']);
        }

        $ok = $cartService->removeItem($request, $itemId);
        $logger->info('Removed cart item via controller', ['item_id' => $itemId, 'success' => $ok]);

        $cart = $cartService->getCart($request);
        $shippingMethod = $request->input('shipping_method', 'standard');
        $totals = $cart ? $priceService->calculate($cart, $shippingMethod) : [
            'subtotal' => 0,
            'total_item_discount' => 0,
            'subtotal_after_item_discount' => 0,
            'coupon_discount' => 0,
            'total_after_coupon' => 0,
            'tax_rate' => config('tax.rate', 20.0),
            'tax_amount' => 0,
            'taxable_amount' => 0,
            'shipping_method' => $shippingMethod,
            'shipping_cost' => 0,
            'final_total' => 0,
            'items' => []
        ];

        if ($request->wantsJson() || $request->ajax()) {
            $items = [];
            if ($cart) {
                foreach ($cart->items as $cartItem) {
                    $items[$cartItem->id] = [
                        'id' => $cartItem->id,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $cartItem->unit_price,
                        'final_price' => $cartItem->final_price ?? $cartItem->unit_price,
                        'discount_amount' => $cartItem->discount_amount ?? 0,
                    ];
                }
            }

            return response()->json([
                'success' => (bool) $ok,
                'message' => $ok ? 'Item removed from cart.' : 'Failed to remove item.',
                'summary' => [
                    'subtotal' => round($totals['subtotal'] ?? 0, 2),
                    'total_discount' => round($totals['total_item_discount'] ?? 0, 2),
                    'subtotal_after_discount' => round($totals['subtotal_after_item_discount'] ?? 0, 2),
                    'coupon_discount' => round($totals['coupon_discount'] ?? 0, 2),
                    'tax' => round($totals['tax_amount'] ?? 0, 2),
                    'shipping' => round($totals['shipping_cost'] ?? 0, 2),
                    'total' => round($totals['final_total'] ?? 0, 2),
                    'itemCount' => $cart ? (int) $cart->items->sum('quantity') : 0,
                    'items' => $items,
                ],
            ]);
        }

        return redirect()->route('cart')->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(ApplyCouponRequest $request, CartService $cartService, CouponService $couponService, LoggerInterface $logger, CartRepo $cartRepo)
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
            $totals = $cartRepo->calculateCartTotals($cart);
            $subtotalAfterItemDiscounts = $totals['total'];
            $couponDiscount = $cart->coupon_discount_amount;
            $totalAfterCoupon = max(0, $subtotalAfterItemDiscounts - $couponDiscount);
            $taxRate = config('tax.rate', 20.0);
            $tax = round($totalAfterCoupon * ($taxRate / (100 + $taxRate)), 2);
            $shipping = 0.00;
            $finalTotal = $totalAfterCoupon + $shipping;

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
                    'taxable_amount' => round($totalAfterCoupon - $tax, 2),
                    'tax_amount' => round($tax, 2),
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

    public function removeCoupon(Request $request, CartService $cartService, CouponService $couponService, LoggerInterface $logger, CartRepo $cartRepo)
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

        $totals = $cartRepo->calculateCartTotals($cart);
        $taxRate = config('tax.rate', 20.0);
        $totalWithTax = $totals['total'];
        $tax = round($totalWithTax * ($taxRate / (100 + $taxRate)), 2);
        $shipping = 0.00;
        $finalTotal = $totalWithTax + $shipping;

        return response()->json([
            'success' => $removed,
            'message' => $removed ? 'Coupon removed.' : 'Failed to remove coupon.',
            'summary' => [
                'subtotal' => round($totals['subtotal'], 2),
                'item_discount' => round($totals['total_discount'], 2),
                'subtotal_after_item_discount' => round($totals['total'], 2),
                'coupon_discount' => 0,
                'total_after_coupon' => round($totals['total'], 2),
                'taxable_amount' => round($totalWithTax - $tax, 2),
                'tax_amount' => round($tax, 2),
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => round($finalTotal, 2),
            ],
        ]);
    }
}

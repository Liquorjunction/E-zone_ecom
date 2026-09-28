<?php

namespace App\Http\Controllers\Client;

use App\DTOs\CartIdentifier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Cart\CartAddRequest;
use App\Http\Requests\Client\Cart\BuyNowRequest;
use App\Http\Requests\Client\Coupon\ApplyCouponRequest;
use App\Repositories\Client\Interfaces\AddressRepositoryInterface as AddressRepo;
use App\Repositories\Client\Interfaces\CartRepositoryInterface as CartRepo;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\SessionCouponService;
use App\Services\PriceCalculatorService;
use App\Support\Ga4Ecommerce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class CartController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Build the summary array that is sent back in every JSON response.
     * Tax is intentionally excluded from the checkout flow.
     */
    private function buildSummary(array $totals, $cart = null): array
    {
        return [
            'subtotal'                     => round($totals['subtotal'] ?? 0, 2),
            'item_discount'                => round($totals['total_item_discount'] ?? 0, 2),
            'subtotal_after_item_discount' => round($totals['subtotal_after_item_discount'] ?? 0, 2),
            'coupon_discount'              => round($totals['coupon_discount'] ?? 0, 2),
            'total_after_coupon'           => round($totals['total_after_coupon'] ?? 0, 2),
            'shipping'                     => round($totals['shipping_cost'] ?? 0, 2),
            'total'                        => round($totals['final_total'] ?? 0, 2),
            'itemCount'                    => $cart ? (int) $cart->items->sum('quantity') : 0,
            'items'                        => $totals['items'] ?? [],
        ];
    }

    private function emptyTotals(string $shippingMethod = 'standard'): array
    {
        return [
            'subtotal'                     => 0,
            'total_item_discount'          => 0,
            'subtotal_after_item_discount' => 0,
            'coupon_discount'              => 0,
            'total_after_coupon'           => 0,
            'shipping_method'              => $shippingMethod,
            'shipping_cost'                => 0,
            'final_total'                  => 0,
            'items'                        => [],
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Add item
    // ─────────────────────────────────────────────────────────────

    public function add(CartAddRequest $request, CartService $cartService, LoggerInterface $logger)
    {
        $data   = $request->validated();
        $cartId = CartIdentifier::fromRequest($request);

        // If a previous buy-now flow was abandoned, move that item back first.
        $cartService->recoverIncompleteBuyNowToPreviousCart($cartId);

        try {
            $item = $cartService->addItem($cartId, $data);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }

        $logger->info('Added item to cart via controller', ['item_id' => $item->id ?? null]);

        if ($request->wantsJson() || $request->ajax()) {
            $cart      = $cartService->getCart($cartId);
            $itemCount = $cart ? (int) $cart->items->sum('quantity') : 0;

            return response()->json([
                'success' => true,
                'message' => statictranslations('Item added to cart.'),
                'item_id' => $item->id ?? null,
                'summary' => ['itemCount' => $itemCount],
            ]);
        }

        return redirect()->back()->with('success', statictranslations('Item added to cart.'));
    }

    public function itemStatus(Request $request, CartService $cartService)
    {
        $productId = (int) $request->input('product_id');
        $variantId = $request->filled('variant_id') ? (int) $request->input('variant_id') : null;

        if ($productId <= 0) {
            return response()->json([
                'success' => false,
                'message' => statictranslations('Invalid product.'),
                'in_cart' => false,
            ], 422);
        }

        $cartId = CartIdentifier::fromRequest($request);
        $cart = $cartService->getCart($cartId);

        $inCart = false;
        if ($cart && $cart->items) {
            $inCart = $cart->items->contains(function ($item) use ($productId, $variantId) {
                if ((int) $item->product_id !== $productId) {
                    return false;
                }

                if ($variantId === null) {
                    return true;
                }

                return (int) ($item->variant_id ?? 0) === $variantId;
            });
        }

        return response()->json([
            'success' => true,
            'in_cart' => $inCart,
        ]);
    }

    public function buyNow(BuyNowRequest $request, CartService $cartService, LoggerInterface $logger)
    {
        $data   = $request->validated();
        $cartId = CartIdentifier::fromRequest($request);

        try {
            $buyNow = $cartService->startBuyNowFlow($cartId, $data);
            $buyNowCart = $buyNow['cart'] ?? null;
            $item = $buyNow['item'] ?? null;
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }

        $logger->info('Started buy-now flow', [
            'cart_id' => $buyNowCart->id ?? null,
            'item_id' => $item->id ?? null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            $itemCount = $buyNowCart ? (int) $buyNowCart->items->sum('quantity') : 0;

            return response()->json([
                'success' => true,
                'message' => statictranslations('Item added. Proceed to checkout.'),
                'item_id' => $item->id ?? null,
                'buy_now' => true,
                'redirect_to' => route('checkout', ['step' => 2, 'buy_now' => 1]),
                'summary' => ['itemCount' => $itemCount],
            ]);
        }

        return redirect()->route('checkout', ['step' => 2, 'buy_now' => 1])
            ->with('success', statictranslations('Item added. Proceed to checkout.'));
    }

    // ─────────────────────────────────────────────────────────────
    // Show cart / checkout page
    // ─────────────────────────────────────────────────────────────

    public function show(
        Request $request,
        CartService $cartService,
        LoggerInterface $logger,
        PriceCalculatorService $priceService,
        SessionCouponService $sessionCoupon,
        AddressRepo $addressRepo
    ) {
        $cartId = CartIdentifier::fromRequest($request);
        $cartService->recoverIncompleteBuyNowToPreviousCart($cartId);
        $cart   = $cartService->getCart($cartId);
        $logger->info('Showing cart', ['cart_id' => $cart->id ?? null]);

        $totals           = $cart ? $priceService->calculate($cart) : $this->emptyTotals();
        $addresses        = Auth::check() ? $addressRepo->getAddressesByUserId(Auth::id()) : collect();
        $billingAddresses = collect();
        $cities           = $addressRepo->getActiveCities() ?: collect();
        $appliedCoupon    = $cart ? $sessionCoupon->getForCart($cart->id) : null;

        $itemsForView = $cart ? $cart->items : collect();
        $ga4Ecommerce = Ga4Ecommerce::cartPayload($cart, $totals);
        $ga4Purchase = null;

        return view('client.pages.checkout-process', compact('cart', 'totals', 'addresses', 'billingAddresses', 'cities', 'appliedCoupon', 'itemsForView', 'ga4Ecommerce', 'ga4Purchase'));
    }

    // ─────────────────────────────────────────────────────────────
    // Update quantity
    // ─────────────────────────────────────────────────────────────

    public function update(
        Request $request,
        CartService $cartService,
        LoggerInterface $logger,
        PriceCalculatorService $priceService
    ) {
        $itemId         = (int) $request->input('item_id');
        $shippingMethod = $request->input('shipping_method', 'standard');
        $cartId         = CartIdentifier::fromRequest($request);

        // Support totals-only refresh requests (e.g. shipping method change on checkout step 2)
        if ($itemId <= 0 && ($request->wantsJson() || $request->ajax())) {
            $cart   = $cartService->getCart($cartId);
            $totals = $cart ? $priceService->calculate($cart, $shippingMethod) : $this->emptyTotals($shippingMethod);

            $itemsMap = [];
            if ($cart) {
                foreach ($cart->items as $cartItem) {
                    $itemsMap[$cartItem->id] = [
                        'id'              => $cartItem->id,
                        'quantity'        => $cartItem->quantity,
                        'unit_price'      => $cartItem->unit_price,
                        'final_price'     => $cartItem->final_price ?? $cartItem->unit_price,
                        'discount_amount' => $cartItem->discount_amount ?? 0,
                    ];
                }
            }

            $summary          = $this->buildSummary($totals, $cart);
            $summary['items'] = $itemsMap;

            return response()->json([
                'success' => true,
                'message' => statictranslations('Summary refreshed.'),
                'item'    => null,
                'summary' => $summary,
            ]);
        }

        $getStock = function ($cartItem) {
            if (! $cartItem) return 0;
            $variant = $cartItem->variant ?? null;
            $product = $cartItem->product ?? null;
            if ($variant) return (int) ($variant->getSellableStock() ?? 0);
            if ($product) {
                return (int) $product->productVariants->sum(function ($pv) {
                    return method_exists($pv, 'getSellableStock') ? $pv->getSellableStock() : 0;
                });
            }
            return 0;
        };

        try {
            $item = $cartService->updateItem($cartId, $itemId, $request->only(['quantity']));
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                $cart        = $cartService->getCart($cartId);
                $currentItem = $cart ? $cart->items->firstWhere('id', $itemId) : null;
                $maxStock    = $getStock($currentItem);

                return response()->json([
                    'success'   => false,
                    'message'   => $e->getMessage(),
                    'item'      => ['id' => $currentItem->id ?? $itemId, 'quantity' => $currentItem->quantity ?? null, 'max_stock' => $maxStock],
                    'max_stock' => $maxStock,
                ], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }

        $logger->info('Updated cart item via controller', ['item_id' => $item->id ?? null]);

        $cart   = $cartService->getCart($cartId);
        $totals = $cart ? $priceService->calculate($cart, $shippingMethod) : $this->emptyTotals($shippingMethod);

        // Build per-item discount info for the updated item
        $itemDiscount = null;
        if ($item && isset($totals['items'][$item->id])) {
            $info         = $totals['items'][$item->id];
            $itemDiscount = [
                'has_discount'              => $info['discount_amount'] > 0,
                'discount_amount'           => $info['discount_amount'],
                'line_total_after_discount' => $info['line_total_after_discount']
                    ?? (($item->final_price ?? $item->unit_price ?? 0) * ($item->quantity ?? 1)),
            ];
        }

        if ($request->wantsJson() || $request->ajax()) {
            // Build keyed items map for the JS updater
            $itemsMap = [];
            if ($cart) {
                foreach ($cart->items as $cartItem) {
                    $itemsMap[$cartItem->id] = [
                        'id'              => $cartItem->id,
                        'quantity'        => $cartItem->quantity,
                        'unit_price'      => $cartItem->unit_price,
                        'final_price'     => $cartItem->final_price ?? $cartItem->unit_price,
                        'discount_amount' => $cartItem->discount_amount ?? 0,
                        'max_stock'       => $getStock($cartItem),
                    ];
                }
            }

            $summary          = $this->buildSummary($totals, $cart);
            $summary['items'] = $itemsMap;

            return response()->json([
                'success' => true,
                'message' => statictranslations('Cart updated.'),
                'item'    => [
                    'id'                         => $item->id ?? null,
                    'quantity'                   => $item->quantity ?? null,
                    'unit_price'                => $item->unit_price ?? null,
                    'line_total'                => isset($item->quantity, $item->final_price)
                        ? ($item->quantity * $item->final_price)
                        : (isset($item->quantity, $item->unit_price) ? ($item->quantity * $item->unit_price) : null),
                    'line_total_before_discount' => isset($item->quantity, $item->unit_price) ? ($item->quantity * $item->unit_price) : null,
                    'discount'                  => $itemDiscount,
                    'max_stock'                 => $getStock($item),
                ],
                'summary' => $summary,
            ]);
        }

        return redirect()->back()->with('success', statictranslations('Cart updated.'));
    }

    // ─────────────────────────────────────────────────────────────
    // Remove item
    // ─────────────────────────────────────────────────────────────

    public function remove(
        Request $request,
        CartService $cartService,
        LoggerInterface $logger,
        PriceCalculatorService $priceService
    ) {
        $itemId = (int) ($request->input('id') ?? $request->input('item_id'));

        if (! $itemId) {
            return response()->json(['success' => false, 'message' => statictranslations('Item ID is missing.')]);
        }

        $cartId         = CartIdentifier::fromRequest($request);
        $shippingMethod = $request->input('shipping_method', 'standard');
        $ok             = $cartService->removeItem($cartId, $itemId);
        $logger->info('Removed cart item via controller', ['item_id' => $itemId, 'success' => $ok]);

        $cart   = $cartService->getCart($cartId);
        $totals = $cart ? $priceService->calculate($cart, $shippingMethod) : $this->emptyTotals($shippingMethod);

        if ($request->wantsJson() || $request->ajax()) {
            // Build keyed items map
            $itemsMap = [];
            if ($cart) {
                foreach ($cart->items as $cartItem) {
                    $itemsMap[$cartItem->id] = [
                        'id'              => $cartItem->id,
                        'quantity'        => $cartItem->quantity,
                        'unit_price'      => $cartItem->unit_price,
                        'final_price'     => $cartItem->final_price ?? $cartItem->unit_price,
                        'discount_amount' => $cartItem->discount_amount ?? 0,
                    ];
                }
            }

            $summary          = $this->buildSummary($totals, $cart);
            $summary['items'] = $itemsMap;

            return response()->json([
                'success' => (bool) $ok,
                'message' => $ok ? statictranslations('Item removed from cart.') : statictranslations('Failed to remove item.'),
                'summary' => $summary,
            ]);
        }

        return redirect()->route('cart')->with('success', statictranslations('Item removed from cart.'));
    }

    // ─────────────────────────────────────────────────────────────
    // Apply coupon
    // ─────────────────────────────────────────────────────────────

    public function applyCoupon(
        ApplyCouponRequest $request,
        CartService $cartService,
        CouponService $couponService,
        LoggerInterface $logger,
        PriceCalculatorService $priceService
    ) {
        $cartId = CartIdentifier::fromRequest($request);
        $cart   = $cartService->getCart($cartId);

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => statictranslations('Your cart is empty.')]);
        }

        $code   = strtoupper($request->input('code'));
        $result = $couponService->validateAndApplyCoupon($code, $cart);

        $logger->info('Coupon application attempt', [
            'cart_id' => $cart->id,
            'code'    => $code,
            'success' => $result['success'],
        ]);

        if ($result['success']) {
            $shippingMethod = $request->input('shipping_method', 'standard');
            $totals         = $priceService->calculate($cart, $shippingMethod);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'coupon'  => $result['coupon'],
                'summary' => $this->buildSummary($totals, $cart),
            ]);
        }

        return response()->json(['success' => false, 'message' => $result['message']]);
    }

    // ─────────────────────────────────────────────────────────────
    // Remove coupon
    // ─────────────────────────────────────────────────────────────

    public function removeCoupon(
        Request $request,
        CartService $cartService,
        CouponService $couponService,
        LoggerInterface $logger,
        PriceCalculatorService $priceService
    ) {
        $cartId = CartIdentifier::fromRequest($request);
        $cart   = $cartService->getCart($cartId);

        if (! $cart) {
            return response()->json(['success' => false, 'message' => statictranslations('Cart not found.')]);
        }

        $removed = $couponService->removeCoupon($cart);
        $logger->info('Coupon removed from cart', ['cart_id' => $cart->id, 'success' => $removed]);

        $shippingMethod = $request->input('shipping_method', 'standard');
        $totals         = $priceService->calculate($cart, $shippingMethod);

        return response()->json([
            'success' => $removed,
            'message' => $removed ? statictranslations('Coupon removed.') : statictranslations('Failed to remove coupon.'),
            'summary' => $this->buildSummary($totals, $cart),
        ]);
    }
}

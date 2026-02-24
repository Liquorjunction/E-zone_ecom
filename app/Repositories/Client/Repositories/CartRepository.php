<?php

namespace App\Repositories\Client\Repositories;

use App\DTOs\CartIdentifier;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\Client\Interfaces\CartRepositoryInterface;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartRepository implements CartRepositoryInterface
{
    public function getCartForUserOrSession(CartIdentifier $identifier)
    {
        if ($identifier->userId) {
            return Cart::with('items.product', 'items.variant')
                ->firstOrCreate(['user_id' => $identifier->userId]);
        }

        return Cart::with('items.product', 'items.variant')
            ->firstOrCreate(['session_id' => $identifier->sessionId]);
    }

    public function addItem($cart, array $data)
    {
        $existing = $cart->items()->where('product_id', $data['product_id'])->where('variant_id', $data['variant_id'] ?? null)->first();

        $discountService = app(DiscountService::class);
        $product = Product::find($data['product_id']);
        $variantId = $data['variant_id'] ?? null;

        $discountData = [];
        if ($product && $variantId) {
            $discountData = $discountService->calculateProductDiscount($product, $variantId, $data['unit_price'] ?? null);
        }

        if ($existing) {
            $existing->quantity = max(1, $existing->quantity + ($data['quantity'] ?? 1));
            $existing->unit_price = $data['unit_price'] ?? $existing->unit_price;
            $existing->discount_id = $discountData['discount_id'] ?? null;
            $existing->discount_amount = $discountData['discount_amount'] ?? 0;
            $existing->final_price = $discountData['final_price'] ?? $existing->unit_price;
            $existing->save();
            return $existing;
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $data['product_id'],
            'variant_id' => $data['variant_id'] ?? null,
            'quantity' => $data['quantity'] ?? 1,
            'unit_price' => $data['unit_price'] ?? null,
            'discount_id' => $discountData['discount_id'] ?? null,
            'discount_amount' => $discountData['discount_amount'] ?? 0,
            'final_price' => $discountData['final_price'] ?? ($data['unit_price'] ?? 0),
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

        $discountService = app(\App\Services\DiscountService::class);
        $product = $item->product;
        $variantId = $item->variant_id;

        if ($product && $variantId) {
            $discountData = $discountService->calculateProductDiscount($product, $variantId, $item->unit_price);
            $item->discount_id = $discountData['discount_id'] ?? null;
            $item->discount_amount = $discountData['discount_amount'] ?? 0;
            $item->final_price = $discountData['final_price'] ?? $item->unit_price;
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

    public function calculateCartTotals($cart): array
    {
        $items = $cart->items()->with(['product', 'variant'])->get();

        $subtotal = 0;
        $totalDiscount = 0;
        $itemsData = [];
        $discountService = app(DiscountService::class);

        foreach ($items as $item) {
            $product = $item->product;
            $variant = $item->variant;
            $quantity = $item->quantity;

            $itemPrice = $variant ? $variant->price : ($product->price ?? 0);
            $discountData = $variant
                ? $discountService->calculateProductDiscount($product, $variant->id)
                : $discountService->calculateProductDiscount($product, null);

            $lineTotal = $itemPrice * $quantity;
            $lineDiscount = ($discountData['discount_amount'] ?? 0) * $quantity;
            $lineFinal = ($discountData['final_price'] ?? $itemPrice) * $quantity;

            $subtotal += $lineTotal;
            $totalDiscount += $lineDiscount;

            $itemsData[$item->id] = [
                'quantity' => $quantity,
                'unit_price' => $itemPrice,
                'discount_amount' => $discountData['discount_amount'] ?? 0,
                'final_price' => $discountData['final_price'] ?? $itemPrice,
                'line_total' => round($lineTotal, 2),
                'line_discount' => round($lineDiscount, 2),
                'line_total_after_discount' => round($lineFinal, 2),
                'discount_info' => $discountData,
            ];
        }

        $couponDiscount = $cart->coupon_discount_amount ?? 0;
        $totalAfterDiscount = $subtotal - $totalDiscount;
        $finalTotal = max(0, $totalAfterDiscount - $couponDiscount);

        return [
            'subtotal' => round($subtotal, 2),
            'total_discount' => round($totalDiscount, 2),
            'total' => round($totalAfterDiscount, 2),
            'coupon_discount' => round($couponDiscount, 2),
            'final_total' => round($finalTotal, 2),
            'items' => $itemsData,
        ];
    }

    public function getOrderDataByOrderId(int $orderId)
    {
        $order = Order::with([
            'items.product',
            'items.variant'
        ])->find($orderId);

        if (!$order) {
            return null;
        }

        $orderData = [
            'order' => $order->toArray(),
            'items' => [],
        ];

        foreach ($order->items as $item) {
            $orderData['items'][] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product' => $item->product ? $item->product->toArray() : null,
                'variant_id' => $item->variant_id,
                'variant' => $item->variant ? $item->variant->toArray() : null,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_id' => $item->discount_id ?? null,
                'discount_amount' => $item->discount_amount ?? 0,
                'final_price' => $item->final_price ?? $item->unit_price,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        }
        return $orderData;
    }

    public function getAllOrdersForUser($userId)
    {
        return Order::with(['items.product', 'items.variant'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        $orders = Order::with(['items.product', 'items.variant'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return $orders;
    }
}

<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Repositories\Client\Interfaces\CartRepositoryInterface;

class PriceCalculatorService
{
    protected CartRepositoryInterface $cartRepo;
    protected SessionCouponService $sessionCoupon;

    public function __construct(CartRepositoryInterface $cartRepo, SessionCouponService $sessionCoupon)
    {
        $this->cartRepo = $cartRepo;
        $this->sessionCoupon = $sessionCoupon;
    }

    public function calculate(Cart|Order $cartOrOrder, string $shippingMethod = 'standard'): array
    {
        // If it's an Order, use order data directly
        if ($cartOrOrder instanceof Order) {
            return $this->calculateFromOrder($cartOrOrder);
        }

        // Otherwise, calculate from Cart
        return $this->calculateFromCart($cartOrOrder, $shippingMethod);
    }

    private function calculateFromCart(Cart $cart, string $shippingMethod = 'standard'): array
    {
        $cartTotals = $this->cartRepo->calculateCartTotals($cart);

        $subtotal = $cartTotals['subtotal'] ?? 0;
        $totalItemDiscount = $cartTotals['total_discount'] ?? 0;

        $subtotalAfterItemDiscount = $cartTotals['total'] ?? 0;

        // Get coupon discount from session instead of database
        $couponDiscount = $this->sessionCoupon->getDiscountAmount($cart->id);
        $totalAfterCoupon = max(0, $subtotalAfterItemDiscount - $couponDiscount);

        $taxRate = config('tax.rate', 20.0);

        $taxAmount = round($totalAfterCoupon * ($taxRate / (100 + $taxRate)), 2);
        $taxableAmount = round($totalAfterCoupon - $taxAmount, 2);

        $shippingCost = match($shippingMethod) {
            'express' => 12.99,
            default => 0.00,
        };

        $finalTotal = round($totalAfterCoupon + $shippingCost, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'total_item_discount' => round($totalItemDiscount, 2),
            'subtotal_after_item_discount' => round($subtotalAfterItemDiscount, 2),

            'coupon_discount' => round($couponDiscount, 2),
            'total_after_coupon' => round($totalAfterCoupon, 2),

            'tax_rate' => $taxRate,
            'tax_amount' => round($taxAmount, 2),
            'taxable_amount' => round($taxableAmount, 2),

            'shipping_method' => $shippingMethod,
            'shipping_cost' => round($shippingCost, 2),

            'final_total' => round($finalTotal, 2),

            'items' => $cartTotals['items'] ?? []
        ];
    }

    private function calculateFromOrder(Order $order): array
    {
        // Use stored order data for calculations
        $subtotal = (float) ($order->subtotal ?? 0);
        $totalItemDiscount = (float) ($order->items->sum(function ($item) {
            return $item->discount_amount ?? 0;
        }));

        $subtotalAfterItemDiscount = (float) ($order->subtotal ?? 0);
        $couponDiscount = (float) ($order->coupon_discount_amount ?? 0);
        $totalAfterCoupon = max(0, $subtotalAfterItemDiscount - $couponDiscount);

        $taxRate = (float) ($order->tax_percentage ?? config('tax.rate', 20.0));
        $taxAmount = (float) ($order->tax_amount ?? 0);
        $taxableAmount = (float) ($order->taxable_amount ?? 0);

        $shippingMethod = $order->shipping_method ?? 'standard';
        $shippingCost = (float) ($order->shipping_cost ?? ($order->shipping_amount ?? 0));

        $finalTotal = (float) ($order->total ?? 0);

        return [
            'subtotal' => round($subtotal, 2),
            'total_item_discount' => round($totalItemDiscount, 2),
            'subtotal_after_item_discount' => round($subtotalAfterItemDiscount, 2),

            'coupon_discount' => round($couponDiscount, 2),
            'total_after_coupon' => round($totalAfterCoupon, 2),

            'tax_rate' => $taxRate,
            'tax_amount' => round($taxAmount, 2),
            'taxable_amount' => round($taxableAmount, 2),

            'shipping_method' => $shippingMethod,
            'shipping_cost' => round($shippingCost, 2),

            'final_total' => round($finalTotal, 2),

            'items' => $order->items ?? []
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Cart;
use App\Models\CouponUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CouponService
{
    /**
     * Validate and apply a coupon code to the cart
     *
     * @param string $code
     * @param Cart $cart
     * @return array ['success' => bool, 'message' => string, 'discount' => float|null]
     */
    public function validateAndApplyCoupon(string $code, Cart $cart): array
    {
        $coupon = Coupon::where('code', strtoupper($code))
            ->where('status', true)
            ->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Invalid coupon code.',
                'discount' => null,
            ];
        }

        // Check if coupon is within valid date range
        $now = Carbon::now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            return [
                'success' => false,
                'message' => 'This coupon is not yet active.',
                'discount' => null,
            ];
        }

        if ($coupon->ends_at && $now->gt($coupon->ends_at)) {
            return [
                'success' => false,
                'message' => 'This coupon has expired.',
                'discount' => null,
            ];
        }

        // Check usage limit
        if ($coupon->usage_limit && $coupon->usages()->count() >= $coupon->usage_limit) {
            return [
                'success' => false,
                'message' => 'This coupon has reached its usage limit.',
                'discount' => null,
            ];
        }

        // Check per-user usage limit
        if (Auth::check() && $coupon->usage_limit_per_user) {
            $userUsageCount = $coupon->usages()
                ->where('user_id', Auth::id())
                ->count();

            if ($userUsageCount >= $coupon->usage_limit_per_user) {
                return [
                    'success' => false,
                    'message' => 'You have already used this coupon the maximum number of times.',
                    'discount' => null,
                ];
            }
        }

        // Calculate cart subtotal
        $totals = $cart->calculateTotals();
        $subtotal = $totals['total']; // Use total after item discounts

        // Check minimum order amount
        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return [
                'success' => false,
                'message' => sprintf(
                    'Minimum order amount of $%s required to use this coupon.',
                    number_format($coupon->min_order_amount, 2)
                ),
                'discount' => null,
            ];
        }

        // Calculate discount amount
        $discountAmount = $this->calculateDiscountAmount($coupon, $subtotal);

        // Apply coupon to cart
        $cart->coupon_id = $coupon->id;
        $cart->coupon_code = $coupon->code;
        $cart->coupon_discount_amount = $discountAmount;
        $cart->save();

        return [
            'success' => true,
            'message' => sprintf('Coupon applied! You saved $%s', number_format($discountAmount, 2)),
            'discount' => $discountAmount,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
            ],
        ];
    }

    /**
     * Calculate the discount amount based on coupon type
     *
     * @param Coupon $coupon
     * @param float $subtotal
     * @return float
     */
    protected function calculateDiscountAmount(Coupon $coupon, float $subtotal): float
    {
        $discount = 0;

        if ($coupon->coupon_type === 'discount') {
            // Percentage discount
            $discount = ($subtotal * $coupon->value) / 100;
        } elseif ($coupon->coupon_type === 'flat_amount') {
            // Fixed amount discount
            $discount = min($coupon->value, $subtotal);
        }

        // Apply max discount cap if set
        if ($coupon->max_discount && $discount > $coupon->max_discount) {
            $discount = $coupon->max_discount;
        }

        // Ensure discount doesn't exceed subtotal
        $discount = min($discount, $subtotal);

        return round($discount, 2);
    }

    /**
     * Remove coupon from cart
     *
     * @param Cart $cart
     * @return bool
     */
    public function removeCoupon(Cart $cart): bool
    {
        $cart->coupon_id = null;
        $cart->coupon_code = null;
        $cart->coupon_discount_amount = 0;

        return $cart->save();
    }

    /**
     * Record coupon usage after order is placed
     *
     * @param int $couponId
     * @param int $orderId
     * @param int|null $userId
     * @return CouponUsage
     */
    public function recordUsage(int $couponId, int $orderId, ?int $userId = null): CouponUsage
    {
        return CouponUsage::create([
            'coupon_id' => $couponId,
            'order_id' => $orderId,
            'user_id' => $userId,
            'used_at' => Carbon::now(),
        ]);
    }

    /**
     * Get coupon by code
     *
     * @param string $code
     * @return Coupon|null
     */
    public function getCouponByCode(string $code): ?Coupon
    {
        return Coupon::where('code', strtoupper($code))
            ->where('status', true)
            ->first();
    }
}

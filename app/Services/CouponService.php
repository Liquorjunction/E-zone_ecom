<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Cart;
use App\Repositories\Client\Interfaces\CartRepositoryInterface;
use App\Models\CouponUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CouponService
{
    protected CartRepositoryInterface $cartRepo;

    public function __construct(CartRepositoryInterface $cartRepo)
    {
        $this->cartRepo = $cartRepo;
    }

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

        if ($coupon->usage_limit && $coupon->usages()->count() >= $coupon->usage_limit) {
            return [
                'success' => false,
                'message' => 'This coupon has reached its usage limit.',
                'discount' => null,
            ];
        }

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

        $hasDiscountedItems = $cart->items()->where('discount_amount', '>', 0)->exists();

        if ($hasDiscountedItems) {
            return [
                'success' => false,
                'message' => 'Coupon cannot be applied to cart with discounted items. Please remove discounted products to use this coupon.',
                'discount' => null,
            ];
        }

        $totals = $this->cartRepo->calculateCartTotals($cart);
        $subtotal = $totals['total'];

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

        $discountAmount = $this->calculateDiscountAmount($coupon, $subtotal);

        // Store coupon details in session
        session()->put('cart.coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount_amount' => $discountAmount,
        ]);

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

        if ($coupon->max_discount && $discount > $coupon->max_discount) {
            $discount = $coupon->max_discount;
        }

        // Ensure discount doesn't exceed subtotal
        $discount = min($discount, $subtotal);

        return round($discount, 2);
    }

    public function removeCoupon(Cart $cart): bool
    {
        $cart->coupon_id = null;
        $cart->coupon_code = null;
        $cart->coupon_discount_amount = 0;

        return $cart->save();
    }

    public function recordUsage(int $couponId, int $orderId, ?int $userId = null): CouponUsage
    {
        return CouponUsage::create([
            'coupon_id' => $couponId,
            'order_id' => $orderId,
            'user_id' => $userId,
            'used_at' => Carbon::now(),
        ]);
    }

    public function getCouponByCode(string $code): ?Coupon
    {
        return Coupon::where('code', strtoupper($code))
            ->where('status', true)
            ->first();
    }

    public function removeCouponFromSession(): void
    {
        session()->forget('cart.coupon');
    }
}

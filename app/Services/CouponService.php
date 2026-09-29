<?php

namespace App\Services;

use App\Repositories\Admin\Interfaces\CouponRepositoryInterface;
use App\Models\Cart;
use App\Repositories\Client\Interfaces\CartRepositoryInterface;
use App\Models\CouponUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CouponService
{
    protected CartRepositoryInterface $cartRepo;
    protected SessionCouponService $sessionCoupon;
    protected CouponRepositoryInterface $couponRepo;

    public function __construct(
        CartRepositoryInterface $cartRepo,
        SessionCouponService $sessionCoupon,
        CouponRepositoryInterface $couponRepo
    ) {
        $this->cartRepo = $cartRepo;
        $this->sessionCoupon = $sessionCoupon;
        $this->couponRepo = $couponRepo;
    }

    public function validateAndApplyCoupon(string $code, Cart $cart): array
    {
        // Remove coupon if cart is empty
        if ($cart->items()->count() === 0) {
            $this->sessionCoupon->remove();
            return [
                'success' => false,
                'message' => statictranslations('Cannot apply a coupon to an empty cart.'),
                'discount' => null,
            ];
        }
        $coupon = $this->couponRepo->getAllCoupons([
            'code' => strtoupper($code),
            'status' => true
        ])->first();

        Log::debug('CouponService: Attempting to apply coupon', [
            'code' => $code,
            'cart_id' => $cart->id,
        ]);

        if (!$coupon) {
            return [
                'success' => false,
                'message' => statictranslations('Invalid coupon code.'),
                'discount' => null,
            ];
        }

        $now = Carbon::now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            return [
                'success' => false,
                'message' => statictranslations('This coupon is not yet active.'),
                'discount' => null,
            ];
        }

        if ($coupon->ends_at && $now->gt($coupon->ends_at)) {
            return [
                'success' => false,
                'message' => statictranslations('This coupon has expired.'),
                'discount' => null,
            ];
        }

        if ($coupon->usage_limit && $coupon->usages()->count() >= $coupon->usage_limit) {
            return [
                'success' => false,
                'message' => statictranslations('This coupon has reached its usage limit.'),
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
                    'message' => statictranslations('You have already used this coupon the maximum number of times.'),
                    'discount' => null,
                ];
            }
        }

        $hasDiscountedItems = $cart->items()->where('discount_amount', '>', 0)->exists();

        if ($hasDiscountedItems) {
            return [
                'success' => false,
                'message' => statictranslations('Coupon cannot be applied to cart with discounted items. Please remove discounted products to use this coupon.'),
                'discount' => null,
            ];
        }

        $totals = $this->cartRepo->calculateCartTotals($cart);
        $subtotal = $totals['total'];

        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return [
                'success' => false,
                'message' => sprintf(
                    statictranslations('Minimum order amount of %s%s required to use this coupon.'),
                    config('app.CURRENCY_SYMBOL'),
                    money_format_number($coupon->min_order_amount, 2, '')
                ),
                'discount' => null,
            ];
        }


        $discountAmount = $this->calculateDiscountAmount($coupon, $subtotal);

        $this->sessionCoupon->store(
            $coupon->id,
            $coupon->code,
            $discountAmount,
            $cart->id
        );

        return [
            'success' => true,
            'message' => sprintf(statictranslations('Coupon applied! You saved %s%s'), config('app.CURRENCY_SYMBOL'), money_format_number($discountAmount, 2, '')),
            'discount' => $discountAmount,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
            ],
        ];
    }

    protected function calculateDiscountAmount($coupon, float $subtotal): float
    {
        $discount = 0;

        if ($coupon->coupon_type === 'discount') {
            $discount = ($subtotal * $coupon->value) / 100;
        } elseif ($coupon->coupon_type === 'flat_amount') {
            $discount = min($coupon->value, $subtotal);
        }

        if ($coupon->max_discount && $discount > $coupon->max_discount) {
            $discount = $coupon->max_discount;
        }

        $discount = min($discount, $subtotal);

        return round($discount, 2);
    }

    public function removeCoupon(Cart $cart): bool
    {
        $this->sessionCoupon->remove();
        return true;
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

    public function getCouponByCode(string $code): ?object
    {
        return $this->couponRepo->getAllCoupons([
            'code' => strtoupper($code),
            'status' => true
        ])->first();
    }
    public static function clearCouponIfCartEmpty(Cart $cart, SessionCouponService $sessionCoupon)
    {
        if ($cart->items()->count() === 0) {
            $sessionCoupon->remove();
        }
    }

    public function getAppliedCoupon(int $cartId): ?array
    {
        $cart = \App\Models\Cart::find($cartId);
        if ($cart) {
            self::clearCouponIfCartEmpty($cart, $this->sessionCoupon);
            if ($cart->items()->count() === 0) {
                return null;
            }
        }
        return $this->sessionCoupon->getForCart($cartId);
    }

    public function getDiscountAmount(int $cartId): float
    {
        return $this->sessionCoupon->getDiscountAmount($cartId);
    }

    public function hasAppliedCoupon(int $cartId): bool
    {
        return $this->sessionCoupon->hasForCart($cartId);
    }
}

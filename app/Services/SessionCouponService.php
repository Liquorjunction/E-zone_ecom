<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class SessionCouponService
{
    private const SESSION_KEY = 'applied_coupon';

    public function store(int $couponId, string $code, float $discountAmount, int $cartId): void
    {
        Session::put(self::SESSION_KEY, [
            'id' => $couponId,
            'code' => strtoupper($code),
            'discount_amount' => round($discountAmount, 2),
            'cart_id' => $cartId,
            'applied_at' => now()->toDateTimeString(),
        ]);
    }

    public function get(): ?array
    {
        return Session::get(self::SESSION_KEY);
    }

    public function getForCart(int $cartId): ?array
    {
        $coupon = $this->get();

        if (!$coupon || ($coupon['cart_id'] ?? null) !== $cartId) {
            return null;
        }

        return $coupon;
    }

    public function has(): bool
    {
        return Session::has(self::SESSION_KEY);
    }
    public function hasForCart(int $cartId): bool
    {
        $coupon = $this->get();
        return $coupon && ($coupon['cart_id'] ?? null) === $cartId;
    }
    public function getCouponId(): ?int
    {
        $coupon = $this->get();
        return $coupon['id'] ?? null;
    }

    public function getCouponCode(): ?string
    {
        $coupon = $this->get();
        return $coupon['code'] ?? null;
    }

    public function getDiscountAmount(int $cartId): float
    {
        $coupon = $this->getForCart($cartId);
        return $coupon['discount_amount'] ?? 0.0;
    }

    public function remove(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function clear(): void
    {
        $this->remove();
    }

    public function updateDiscountAmount(float $discountAmount, int $cartId): bool
    {
        $coupon = $this->getForCart($cartId);

        if (!$coupon) {
            return false;
        }

        $coupon['discount_amount'] = round($discountAmount, 2);
        Session::put(self::SESSION_KEY, $coupon);

        return true;
    }

    public function toArray(): array
    {
        return $this->get() ?? [];
    }
}

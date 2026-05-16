<?php

use App\Services\DiscountService;

if (!function_exists('clear_product_discount_cache')) {
    /**
     * Clear discount cache for a specific product
     *
     * @param int $productId
     * @return void
     */
    function clear_product_discount_cache(int $productId): void
    {
        $discountService = app(DiscountService::class);
        $discountService->clearProductDiscountCache($productId);
    }
}

if (!function_exists('clear_all_discount_caches')) {
    /**
     * Clear all discount caches
     * Should be called after creating/updating/deleting discounts
     *
     * @return void
     */
    function clear_all_discount_caches(): void
    {
        $discountService = app(DiscountService::class);
        $discountService->clearAllDiscountCaches();
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format price with currency symbol
     *
     * @param float $amount
     * @param bool $showSymbol
     * @return string
     */
    function format_currency(float $amount, bool $showSymbol = true): string
    {
        $formatted = number_format($amount, 2);
        return $showSymbol ? config('app.CURRENCY_SYMBOL', '$') . $formatted : $formatted;
    }
}

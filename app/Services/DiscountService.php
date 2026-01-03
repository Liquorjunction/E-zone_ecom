<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DiscountService
 *
 * Implements "Best Discount Wins" strategy
 * - Fetches all applicable discounts (brand, category, subcategory, product)
 * - Returns the discount that gives maximum savings to the customer
 */
class DiscountService
{
    // Helped method to get all active discounts from DB
    public function getActiveDiscounts()
    {
        $count = Discount::query()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->where('status', true)
            ->orWhere(function ($query) {
                $query->where('starts_at', '>=', now())
                      ->where('status', true);
            })
            ->count();
        Log::info("Total discounts available: {$count}");
        $discounts = Discount::query()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->where('status', true)
            ->orWhere(function ($query) {
                $query->where('starts_at', '>=', now())
                      ->where('status', true);
            })
            ->get();

            Log::info("Discounts: ", $discounts->toArray());
        return $discounts;
    }


    // Helper method to check the getActiveDiscounts() is applicable to a given product
    public function checkApplicableDiscounts(Product $product)
    {
        // Here we will determine which discounts apply to the given product from getActiveDiscounts()
        $applicableDiscounts = [];
        $discounts = $this->getActiveDiscounts();

        // Check each discount against the product with SQl queries
        foreach ($discounts as $discount) {
            $isApplicable = false;

            // Check brand discount
            if ($discount->brand_id === $product->brand_id) {
                $isApplicable = true;
                Log::info("Applicable brand discount found: Discount ID {$discount->id} for Product ID {$product->id}");
            }

            // Check category discount
            if ($discount->category_id === $product->category_id) {
                $isApplicable = true;
                Log::info("Applicable category discount found: Discount ID {$discount->id} for Product ID {$product->id}");
            }
            // Check subcategory discount
            if ($discount->subcategory_id === $product->subcategory_id) {
                $isApplicable = true;
                Log::info("Applicable subcategory discount found: Discount ID {$discount->id} for Product ID {$product->id}");
            }

            // Check product discount
            if ($discount->product_id === $product->id) {
                $isApplicable = true;
                Log::info("Applicable product discount found: Discount ID {$discount->id} for Product ID {$product->id}");
            }
            if ($isApplicable) {
                $applicableDiscounts[] = $discount;
                Log::info("Discount ID {$discount->id} added to applicable discounts for Product ID {$product->id}");
            }
        }
        return $applicableDiscounts;
    }

    // Main method to get the best discount for a product using both helper methods
    public function getBestDiscount(Product $product)
    {
        $applicableDiscounts = $this->checkApplicableDiscounts($product);
        return $applicableDiscounts;

        // Determine the best discount (maximum savings)
        $bestDiscount = null;
        $maxSavings = 0;
        foreach ($applicableDiscounts as $discount) {
            $savings = $discount->amount;
            if ($savings > $maxSavings) {
                $bestDiscount = $discount;
                $maxSavings = $savings;
            }
        }
        return $bestDiscount;
    }

    // Helped method to clear cache when discounts are updated
    public function clearAllDiscountCaches(): void
    {
        // You might want to implement a more sophisticated cache clearing strategy
        // For now, this is a placeholder
        Cache::flush();
        Log::info('All discount caches cleared');
    }
}

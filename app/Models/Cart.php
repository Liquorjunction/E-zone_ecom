<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id'
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate cart totals with discounts applied
     * Uses DiscountService with "Best Discount Wins" logic
     *
     * @return array
     */
    public function calculateTotals(): array
    {
        $discountService = app(\App\Services\DiscountService::class);
        return $discountService->calculateCartTotals($this->items()->with(['product', 'variant'])->get());
    }

    /**
     * Get cart subtotal (before discounts)
     *
     * @return float
     */
    public function getSubtotal(): float
    {
        $totals = $this->calculateTotals();
        return $totals['subtotal'];
    }

    /**
     * Get total discount amount
     *
     * @return float
     */
    public function getTotalDiscount(): float
    {
        $totals = $this->calculateTotals();
        return $totals['total_discount'];
    }

    /**
     * Get cart total (after discounts)
     *
     * @return float
     */
    public function getTotal(): float
    {
        $totals = $this->calculateTotals();
        return $totals['total'];
    }

    /**
     * Check if cart has any items with discounts
     *
     * @return bool
     */
    public function hasDiscounts(): bool
    {
        $totals = $this->calculateTotals();
        return $totals['total_discount'] > 0;
    }
}

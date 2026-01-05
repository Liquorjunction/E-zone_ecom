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
     * Uses stored discount data from cart_items table
     *
     * @return array
     */
    public function calculateTotals(): array
    {
        $items = $this->items()->with(['product', 'variant'])->get();

        $subtotal = 0;
        $totalDiscount = 0;
        $itemsData = [];

        foreach ($items as $item) {
            $lineTotal = $item->unit_price * $item->quantity;
            $lineDiscount = $item->discount_amount * $item->quantity;
            $lineFinal = $item->final_price * $item->quantity;

            $subtotal += $lineTotal;
            $totalDiscount += $lineDiscount;

            $itemsData[$item->id] = [
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_amount' => $item->discount_amount,
                'final_price' => $item->final_price,
                'line_total' => round($lineTotal, 2),
                'line_discount' => round($lineDiscount, 2),
                'line_total_after_discount' => round($lineFinal, 2),
            ];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'total_discount' => round($totalDiscount, 2),
            'total' => round($subtotal - $totalDiscount, 2),
            'items' => $itemsData,
        ];
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

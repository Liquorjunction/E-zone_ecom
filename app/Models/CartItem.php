<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'unit_price'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get the applicable price (variant price takes precedence)
     *
     * @return float
     */
    public function getApplicablePrice(): float
    {
        if ($this->variant) {
            return $this->variant->price;
        }
        return $this->product->price;
    }

    /**
     * Get discount information for this cart item
     *
     * @return array
     */
    public function getDiscountInfo(): array
    {
        $discountService = app(\App\Services\DiscountService::class);

        if ($this->variant) {
            return $discountService->calculateVariantDiscount($this->variant);
        }

        return $discountService->calculateProductDiscount($this->product);
    }

    /**
     * Get line total (before discount)
     *
     * @return float
     */
    public function getLineTotal(): float
    {
        return $this->getApplicablePrice() * $this->quantity;
    }

    /**
     * Get line discount amount
     *
     * @return float
     */
    public function getLineDiscount(): float
    {
        $discountInfo = $this->getDiscountInfo();
        return $discountInfo['discount_amount'] * $this->quantity;
    }

    /**
     * Get line final price (after discount)
     *
     * @return float
     */
    public function getLineFinal(): float
    {
        $discountInfo = $this->getDiscountInfo();
        return $discountInfo['final_price'] * $this->quantity;
    }

    /**
     * Check if this item has a discount
     *
     * @return bool
     */
    public function hasDiscount(): bool
    {
        $discountInfo = $this->getDiscountInfo();
        return $discountInfo['has_discount'];
    }
}

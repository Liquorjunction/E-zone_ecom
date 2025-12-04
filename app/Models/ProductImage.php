<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    use SoftDeletes;

    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'image_path',
        'is_primary',
        'status'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeForProduct($query, $productId)
    {
        // Gets images assigned to the main product (not specific variants)
        return $query->where('product_id', $productId)->whereNull('product_variant_id');
    }

    public function scopeForVariant($query, $variantId)
    {
        return $query->where('product_variant_id', $variantId);
    }
}

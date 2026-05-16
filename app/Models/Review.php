<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reviews';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'order_id',
        'order_item_id',
        'rating',
        'title',
        'review_text',
        'reviewer_name',
        'reviewer_email',
        'is_verified_purchase',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'float',
        'is_verified_purchase' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(ReviewImage::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', true);
    }
}

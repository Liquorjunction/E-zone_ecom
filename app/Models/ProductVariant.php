<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class ProductVariant extends Model
{
    use HasSlug, SoftDeletes, HasTranslations, HasFactory;

    protected $table = 'product_variants';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = [
        'variant_name',
        'attributes'
    ];

    protected $fillable = [
        'product_id',
        'variant_name',
        'sku',
        'slug',
        'price',
        'attributes',
        'is_bestseller',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'attributes' => 'array',
        'is_bestseller' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function($model) {
                $name = $model->getTranslation('variant_name', 'en');
                return !empty($name) ? $name : $model->sku;
            })
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50)
            ->doNotGenerateSlugsOnUpdate();
    }

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_variant_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getTotalStockAttribute()
    {
        return $this->inventory()->sum('quantity');
    }

    public function getDiscountInfo(): array
    {
        $discountService = app(\App\Services\DiscountService::class);
        return $discountService->calculateVariantDiscount($this);
    }

    public function getFinalPrice(): float
    {
        $discountInfo = $this->getDiscountInfo();
        return $discountInfo['final_price'];
    }

    public function hasDiscount(): bool
    {
        $discountInfo = $this->getDiscountInfo();
        return $discountInfo['has_discount'];
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'variant_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_variant_id');
    }

    public function discountTargets()
    {
        return $this->morphMany(DiscountTarget::class, 'target');
    }
}

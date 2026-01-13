<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasSlug, HasTranslations, HasFactory, SoftDeletes;

    protected $table = 'products';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = [
        'name',
        'description',
        'dimensions',
        'specifications',
        'warranty'
    ];

    protected $fillable = [
        'brand_id',
        'category_id',
        'sub_category_id',
        'name',
        'slug',
        'sku',
        'description',
        'cover_image',
        'price',
        'dimensions',
        'specifications',
        'warranty',
        'status',
        'is_bestseller'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_bestseller' => 'boolean',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function($model) {
                return $model->getTranslation('name', 'en');
            })
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50)
            ->doNotGenerateSlugsOnUpdate();
    }

    // Relationships
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategories::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function discountTargets()
    {
        return $this->morphMany(DiscountTarget::class, 'target');
    }


    // --- Helpers ---
    public function getRealPriceAttribute()
    {
        if ($this->relationLoaded('productVariants')) {
            return $this->productVariants->min('price');
        }
        return $this->price;
    }

    public function getPriceDataAttribute()
    {
        return $this->getDiscountInfo();
    }

    public function getDiscountInfo(): array
    {
        $firstVariant = $this->productVariants()->first();
        $variantId = $firstVariant ? $firstVariant->id : 0;

        if (!$firstVariant) {
            return [
                'has_discount' => false,
                'original_price' => $this->price ?? 0,
                'final_price' => $this->price ?? 0,
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'discount_name' => null,
                'discount_id' => null,
            ];
        }

        $discountService = app(\App\Services\DiscountService::class);
        return $discountService->calculateProductDiscount($this, $variantId);
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

    public function calculateBestPrice()
    {
        return $this->getDiscountInfo();
    }

    public function findBestDiscount()
    {
        $discountService = app(\App\Services\DiscountService::class);
        return $discountService->findBestDiscountForProduct($this);
    }
}

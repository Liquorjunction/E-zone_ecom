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

    // Spatie Translatable Fields
    public $translatable = [
        'name',
        'description',
        'dimensions',
        'specifications', // Stores as JSON, handled by Spatie
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
        'is_bestseller',
    ];

    // Standard Casts
    // Note: Do NOT cast 'translatable' fields to 'array' here if using HasTranslations.
    // Spatie handles the serialization automatically.
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

    /**
     * Get the actual selling price range or starting price
     */
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

    /**
     * Get discount information using DiscountService
     * "Best Discount Wins" logic applied
     *
     * @return array
     */
    public function getDiscountInfo(): array
    {
        $discountService = app(\App\Services\DiscountService::class);
        return $discountService->calculateProductDiscount($this);
    }

    /**
     * Get the final price after discount
     *
     * @return float
     */
    public function getFinalPrice(): float
    {
        $discountInfo = $this->getDiscountInfo();
        return $discountInfo['final_price'];
    }

    /**
     * Check if product has active discount
     *
     * @return bool
     */
    public function hasDiscount(): bool
    {
        $discountInfo = $this->getDiscountInfo();
        return $discountInfo['has_discount'];
    }

    /**
     * Core Logic: Find best discount among Brand, Category, SubCategory, and Product rules.
     * @deprecated Use DiscountService instead for better caching and separation of concerns
     */
    public function calculateBestPrice()
    {
        // Keep for backward compatibility, but delegate to service
        return $this->getDiscountInfo();
    }

    /**
     * Query to find the single winning discount model.
     * @deprecated Use DiscountService instead
     */
    public function findBestDiscount()
    {
        $discountService = app(\App\Services\DiscountService::class);
        return $discountService->findBestDiscountForProduct($this);
    }
}

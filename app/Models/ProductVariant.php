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

    // Fixed: Matches migration table name
    protected $table = 'product_variants';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = [
        'variant_name',
        'attributes'
    ];

    protected $fillable = [
        'product_id',
        'variant_name', // e.g. "Red / Large"
        'sku',          // Unique Transactional SKU
        'slug',
        'price',        // The actual selling price
        'attributes',   // JSON: {"Color": "Red", "Size": "L"}
        'status'
    ];

    // Casts: price as decimal and attributes as array to avoid double-encoded JSON issues
    protected $casts = [
        'price' => 'decimal:2',
        'attributes' => 'array',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function($model) {
                // If variant name is empty, fallback to parent product name + sku suffix
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

    public function images()
    {
        // Images specific to this variant
        return $this->hasMany(ProductImage::class, 'product_variant_id');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get Total Stock across all warehouses
     */
    public function getTotalStockAttribute()
    {
        return $this->inventory()->sum('quantity');
    }
}

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
        'variant_name',             // Fixed: was varient_name
        'sku',              // Fixed: was varient_sku
        'slug',             // Fixed: was varient_slug
        'price',            // Fixed: was varient_price
        'attributes',       // Added: matches migration JSON column
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name') // Fixed column name
            ->saveSlugsTo('slug')       // Fixed column name
            ->doNotGenerateSlugsOnUpdate();
    }

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_variant_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

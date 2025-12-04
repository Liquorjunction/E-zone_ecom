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
        'dimensions',       // JSON in migration
        'specifications',   // JSON in migration
        'warranty',
        'status',           // Added (was missing)
    ];

    // Automatically cast JSON columns to Arrays
    protected $casts = [
        'dimensions' => 'array',
        'specifications' => 'array',
        'price' => 'decimal:2',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function($model) {
                // Handle translation if name is translatable, otherwise just use $model->name
                return $model->getTranslation('name', 'en');
            })
            ->saveSlugsTo('slug');
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
}

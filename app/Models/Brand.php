<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Brand extends Model
{
    use HasTranslations, HasRoles, Notifiable, SoftDeletes, HasSlug;

    protected $table = 'brands';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = ['name', 'tagline', 'description'];

    protected $fillable = [
        'name',
        'image',
        'slug',
        'tagline',
        'description',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function banners()
    {
        return $this->hasMany(BrandBanner::class)->orderBy('sort_order', 'asc');
    }

    public function activeBanners()
    {
        return $this->hasMany(BrandBanner::class)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc');
    }

    public function categoryShowcases()
    {
        return $this->hasMany(BrandCategoryShowcase::class)->orderBy('sort_order', 'asc');
    }

    public function activeCategoryShowcases()
    {
        return $this->hasMany(BrandCategoryShowcase::class)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc');
    }

    public function variants()
    {
        return $this->hasManyThrough(
            ProductVariant::class,
            Product::class,
            'brand_id',
            'product_id',
            'id',
            'id'
        );
    }

    public function discountTargets()
    {
        return $this->hasMany(\App\Models\DiscountTarget::class, 'target_id')
            ->where('target_type', 'brand');
    }
}

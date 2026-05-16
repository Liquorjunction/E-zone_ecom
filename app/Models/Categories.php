<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class Categories extends Model
{
    use HasSlug, SoftDeletes, HasRoles, Notifiable, HasTranslations;

    protected $table = 'categories';
    public $guard_name = 'admin';
    protected $guarded = [];

    // Define translatable field
    public $translatable = ['category-name'];

    protected $fillable = [
        'category-name',
        'category-icon',
        'slug',
        'status'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                return $model->getTranslation('category-name', 'en');
            })
            ->saveSlugsTo('slug');
    }

    public function subcategories()
    {
        return $this->hasMany(SubCategories::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function variants()
    {
        return $this->hasManyThrough(
            ProductVariant::class,
            Product::class,
            'category_id',
            'product_id',
            'id',
            'id'
        );
    }

    public function discountTargets()
    {
        return $this->hasMany(\App\Models\DiscountTarget::class, 'target_id')
            ->where('target_type', 'category');
    }
}

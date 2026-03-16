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

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'image',
        'slug',
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
}

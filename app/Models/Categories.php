<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations; // <--- Import Trait

class Categories extends Model
{
    use HasSlug, SoftDeletes, HasRoles, Notifiable, HasTranslations; // <--- Add Trait

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

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            // FIX: Explicitly tell the slug generator to use ONLY the English translation.
            // If we don't do this, it tries to slugify the JSON string '{"en":"Food"}' -> "en-food"
            ->generateSlugsFrom(function($model) {
                return $model->getTranslation('category-name', 'en');
            })
            ->saveSlugsTo('slug');
    }

    public function subcategories()
    {
        return $this->hasMany(SubCategories::class, 'category_id');
    }
}

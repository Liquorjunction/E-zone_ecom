<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations; // <--- Import Trait

class SubCategories extends Model
{
    use HasRoles, HasSlug, Notifiable, SoftDeletes, HasTranslations; // <--- Add Trait

    protected $table = 'sub-categories';
    public $guard_name = 'admin';
    protected $guarded = [];

    // Define translatable field
    public $translatable = ['sub-category-name'];

    protected $fillable = [
        'sub-category-name',
        'sub-category-icon',
        'category_id',
        'slug',
        'status',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            // FIX: Generate slug ONLY from the English translation
            ->generateSlugsFrom(function($model) {
                return $model->getTranslation('sub-category-name', 'en');
            })
            ->saveSlugsTo('slug');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }
}

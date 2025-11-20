<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Categories extends Model
{
    use HasSlug, SoftDeletes , HasRoles, Notifiable;

    protected $table = 'categories';
    public $guard_name = 'admin';
    protected $guarded = [];

    protected $fillable = [
        'category-name',
        'category-icon',
        'slug',
        'status'
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('category-name')
            ->saveSlugsTo('slug');
    }

    public function subcategories()
    {
        return $this->hasMany(SubCategories::class);
    }
}

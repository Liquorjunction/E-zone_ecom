<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class SubCategories extends Model
{
    use HasRoles, HasSlug , Notifiable, SoftDeletes;

    protected $table = 'sub-categories';
    public $guard_name = 'admin';
    protected $guarded = [];

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
            ->generateSlugsFrom('sub-category-name')
            ->saveSlugsTo('slug');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Showrooms extends Model
{
    use HasSlug, HasTranslations, SoftDeletes, HasRoles, HasFactory;

    protected $table = 'showrooms';
    public $guard_name = 'admin';
    protected $guarded = [''];

    public $translatable = [
        'name',
        'description',
        'address',
        'city',
        'state',
        'country',
    ];

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'map_iframe',
        'description',
        'opening_hours',
        'slug',
        'cover_image',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'opening_hours' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            // FIX: Generate slug ONLY from the English translation
            ->generateSlugsFrom(function($model) {
                return $model->getTranslation('name', 'en');
            })
            ->saveSlugsTo('slug');
    }

    public function images()
    {
        return $this->hasMany(ShowroomImages::class);
    }
}

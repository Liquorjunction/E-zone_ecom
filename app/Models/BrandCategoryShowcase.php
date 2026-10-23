<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class BrandCategoryShowcase extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'brand_category_showcases';

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'brand_id',
        'category_id',
        'sub_category_id',
        'slug',
        'title',
        'description',
        'image',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategories::class, 'sub_category_id');
    }
}

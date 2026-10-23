<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class BrandBanner extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'brand_banners';

    public $translatable = ['alt_text'];

    protected $fillable = [
        'brand_id',
        'image',
        'alt_text',
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
}

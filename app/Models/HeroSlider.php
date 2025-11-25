<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations; // <--- Import Trait

class HeroSlider extends Model
{
    use HasFactory, SoftDeletes, HasTranslations; // <--- Add Trait

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hero_slider';

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['title', 'subtitle', 'button_text']; // <--- Define Translatable Fields

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'subtitle',
        'button_text',
        'button_link',
        'image',
        'sort_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}

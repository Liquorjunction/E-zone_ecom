<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Translatable\HasTranslations;

class WhyShopWithUs extends Model
{
    use HasFactory, Notifiable, SoftDeletes, HasTranslations;

    protected $table = 'why_shop_with_us';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'icon',
        'title',
        'description',
        'order',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

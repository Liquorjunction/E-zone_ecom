<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ShippingZones extends Model
{
     use HasTranslations, SoftDeletes;

    protected $table = 'shipping_zones';
    public $translatable = ['zone_name'];
    protected $fillable = [
        'zone_name',
        'shipping_charge',
        'free_shipping_min_cart',
        'status',
    ];

    protected $casts = [
        'shipping_charge' => 'decimal:2',
        'free_shipping_min_cart' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function areas()
    {
        return $this->hasMany(ShippingZoneAreas::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

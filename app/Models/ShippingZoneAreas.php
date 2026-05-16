<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingZoneAreas extends Model
{
    use SoftDeletes;

    protected $table = 'shipping_zone_areas';
    protected $fillable = [
        'shipping_zone_id',
        'area_code',
    ];
    public function shippingZone()
    {
        return $this->belongsTo(ShippingZones::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

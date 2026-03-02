<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'facebook',
        'twitter',
        'instagram',
        'infotext',
        'opening_hours',
        'map_iframe',
        'site_status',
    ];

    protected $casts = [
        'site_status' => 'string',
    ];

    public function scopeActive($query)
    {
        return $query->where('site_status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('site_status', 'inactive');
    }
}

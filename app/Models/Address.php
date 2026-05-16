<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'type',
        'user_id',
        'is_default',
        'address_label',
        'name',
        'phone',
        'street',
        'city',
        'city_id',
        'state',
        'postal_code',
        'country',
    ];

    public function cityRelation()
    {
        return $this->belongsTo(\App\Models\city::class, 'city_id');
    }
}

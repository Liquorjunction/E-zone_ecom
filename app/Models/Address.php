<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'type',
        'user_id',
        'address_label',
        'name',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
    ];
}

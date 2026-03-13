<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class city extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'cities';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = ['city_name', 'country_name'];

    protected $fillable = [
        'city_name',
        'country_name',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Relations 
    public function Addresses()
    {
        return $this->hasMany(Address::class, 'city_id');
    }

}

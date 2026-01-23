<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Store extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $table = 'stores';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = ['store_name', 'store_footer'];

    protected $fillable = [
        'store_name',
        'store_logo',
        'store_id',
        'store_footer',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}

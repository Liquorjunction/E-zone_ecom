<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Warehouse extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $table = 'warehouses';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = [
        'warehouse_name',
        'location',
    ];

    protected $fillable = [
        'warehouse_name',
        'warehouse_id',
        'location',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventories';
    public $guard_name = 'admin';
    protected $guarded = [];

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'store_id',
        'warehouse_id',
        'quantity',
        'batch_no',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'status' => 'string',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDepleted($query)
    {
        return $query->where('status', 'depleted');
    }
}

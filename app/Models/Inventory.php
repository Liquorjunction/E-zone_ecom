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
        'location_type',
        'store_id',
        'warehouse_id',
        'cost_price',
        'quantity',
        'reserved_quantity',
        'batch_no',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved_quantity' => 'decimal:3',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // --- Relationships ---

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getProductNameAttribute()
    {
        return optional($this->product)->name ?? optional(optional($this->variant)->product)->name ?? 'Unknown Product';
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'inventory_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id', 'product_variant_id');
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDepleted($query)
    {
        return $query->where('status', 'depleted')->orWhere('quantity', '<=', 0);
    }

    public function scopeForWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }
}

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
        'batch_no',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // --- Relationships ---

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the Product via the Variant.
     * Useful for displaying "Product Name" in inventory lists.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Provide a `product` attribute so callers can use `$inventory->product->name`.
     * This mirrors the product relation via the variant (product master is variant-based).
     */
    // Remove custom accessor so the `product` relation can be eager-loaded normally.
    // If a fallback is needed, use `getProductNameAttribute()` below.
    // Accessor for easy usage in Blade: $inventory->product_name
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

    /**
     * Transaction history for this inventory record.
     */
    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'inventory_id');
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

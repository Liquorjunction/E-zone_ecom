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
        'product_variant_id', // Strictly required now
        'store_id',
        'warehouse_id',
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
        // Defines a "Has One Through" reversed, or we can just chain it.
        // Since belongsToThrough isn't standard Eloquent, we use the accessor or chained relationship.
        return $this->variant->product();
    }

    // Accessor for easy usage in Blade: $inventory->product_name
    public function getProductNameAttribute()
    {
        return $this->variant->product->name ?? 'Unknown Product';
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
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

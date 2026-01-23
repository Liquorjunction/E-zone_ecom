<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'type',
        'quantity',
        'rate',
        'batch_no',
        'created_by',
        'description',
        // fields added by later migrations
        'reference_type',
        'reference_id',
        'before_quantity',
        'after_quantity',
        'quantity_change'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function order()
    {
        if ($this->reference_type === 'order' && $this->reference_id) {
            return $this->belongsTo(Order::class, 'reference_id');
        }
        return $this->belongsTo(Order::class, 'reference_id')->whereRaw('1=0');
    }
}

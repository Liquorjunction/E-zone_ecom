<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispatch extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'order_dispatch';
    protected $fillable = [
        'order_id',
        'order_item_id',
        'inventory_id',
        'quantity',
        'status',
        'dispatch_method',
        'dispatch_from_store_id',
        'dispatch_from_warehouse_id',
        'dispatched_at',
        'dispatched_by',
        'remarks',
        'delivered_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function dispatchedBy()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'dispatch_from_store_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'dispatch_from_warehouse_id');
    }

}

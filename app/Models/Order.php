<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'subtotal',
        'discount_amount',
        'coupon_id',
        'coupon_code',
        'coupon_discount_amount',
        'shipping_method',
        'shipping_cost',
        'tax_amount',
        'tax_percentage',
        'taxable_amount',
        'total',
        'status',
        'status_changed_at',
        'status_changed_by',
        'cancel_reason',
        'cancelled_by',
        'cancelled_at',
        'shipping_address',
        'billing_address',
        'phone_no',
        'email',
        'payment_method',
        'transaction_id',
        'naps_auth_number',
        'naps_refund_id',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'coupon_discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'status_changed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];


    // Alias for compatibility with code expecting 'orderItems' relationship
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(Admin::class, 'cancelled_by');
    }
}

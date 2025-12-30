<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use SoftDeletes;

    protected $table = 'coupons';

    protected $fillable = [
        'name',
        'code',
        'coupon_type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'usage_limit_per_user',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use SoftDeletes, HasTranslations; // Removed HasRoles

    protected $table = 'discounts';

    // translatable fields defined in migration
    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'type',         // 'flat' or 'percentage'
        'value',
        'max_discount',
        'starts_at',
        'ends_at',
        'status'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'status' => 'boolean',
    ];

    // RELATIONSHIPS

    /**
     * Get the targets (brands, categories, products) this discount applies to.
     */
    public function targets(): HasMany
    {
        return $this->hasMany(DiscountTarget::class);
    }

    /**
     * Get the coupons associated with this discount.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponsUsage::class);
    }

    // HELPER

    /**
     * Check if the discount is currently valid based on dates and status.
     */
    public function isValid(): bool
    {
        if (!$this->status) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DiscountTarget extends Model
{
    protected $table = 'discount_targets';
    protected $guarded = [];

    protected $fillable = [
        'discount_id',
        'target_type',
        'target_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Get the target model (brand, category, subcategory, or product) manually.
     * You may want to implement logic here based on target_type.
     */
    public function target(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'target_type', 'target_id');
    }
}

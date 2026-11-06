<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestUser extends Model
{
    use HasFactory;

    protected $table = 'guest_users';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'token',
        'orders_count',
        'last_order_at',
        'registered_user_id',
    ];

    protected $casts = [
        'orders_count' => 'integer',
        'last_order_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (GuestUser $guest) {
            if (empty($guest->token)) {
                $guest->token = \Illuminate\Support\Str::random(40);
            }
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'guest_user_id');
    }

    public function registeredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_user_id');
    }

    public function isLinked(): bool
    {
        return !is_null($this->registered_user_id);
    }
}

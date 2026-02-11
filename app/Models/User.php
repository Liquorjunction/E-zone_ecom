<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'google_id',
        'apple_id',
        'avatar',
        'is_active',
        'is_newsletter_subscribed',
        'last_login_at',
        'email_verified_at',
        'password_reset_token',
        'password_reset_expires_at',
        'password_reset_requested_at',
        'email_verification_token',
        'email_verification_expires_at',
        'email_verification_requested_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
        'apple_id',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_newsletter_subscribed' => 'boolean',
            'last_login_at' => 'datetime',
            'password_reset_expires_at' => 'datetime',
            'password_reset_requested_at' => 'datetime',
            'email_verification_expires_at' => 'datetime',
            'email_verification_requested_at' => 'datetime',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

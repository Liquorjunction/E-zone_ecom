<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 2. Add "implements MustVerifyEmail"
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
        'email_verified_at', // Ensure this is fillable for Social Login logic
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
        ];
    }
}

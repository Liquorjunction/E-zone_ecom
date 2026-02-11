<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles, Notifiable;

    public $guard_name = 'admin';
    protected $guarded = [];
    protected $table = 'admin';

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'date_of_birth',
        'remarks',
        'password',
        'status',
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
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'password_reset_expires_at' => 'datetime',
            'password_reset_requested_at' => 'datetime',
            'email_verification_expires_at' => 'datetime',
            'email_verification_requested_at' => 'datetime',
        ];
    }
}

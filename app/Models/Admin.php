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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}

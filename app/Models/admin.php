<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles;

    public $guard_name = 'admin';
    protected $guarded = [];
    protected $table = 'admin';

    protected $fillable = [
        'name',
        'username',
        'profile_image',
        'email',
        'status',
        'password',
    ];
}

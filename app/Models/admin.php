<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class admin extends Authenticatable
{
    use HasRoles;

    protected $guarded = 'admin';

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

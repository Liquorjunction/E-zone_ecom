<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Card extends Model
{
    use HasRoles, SoftDeletes, Notifiable;

    protected $table = 'cards';
    public $guard_name = 'admin';
    protected $guarded = [];

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'link_text',
        'tags',
        'status',
        'sort_order',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

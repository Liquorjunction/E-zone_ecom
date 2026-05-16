<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class AboutUs extends Model
{
    use HasTranslations, HasFactory, SoftDeletes, HasRoles;

    protected $table = 'about_us';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

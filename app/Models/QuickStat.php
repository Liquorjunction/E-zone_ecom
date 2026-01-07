<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class QuickStat extends Model
{
    use HasFactory, SoftDeletes, HasTranslations, HasRoles;

    protected $table = 'quick_stats';

    public $guard_name = 'admin';
    public $translatable = ['text1', 'text2'];

    protected $fillable = [
        'icon',
        'text1',
        'text2',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

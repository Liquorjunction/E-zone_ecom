<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TermsAndCondition extends Model
{
    use HasFactory , SoftDeletes, HasTranslations;

    protected $guarded = [];
    public $guard_name = 'admin';
    protected $table = 'terms_and_conditions';

    public $translatable = ['title','content'];

    public $fillable = [
        'title',
        'content',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

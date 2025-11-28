<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasFactory, Notifiable, SoftDeletes, HasTranslations;

    protected $table = 'faqs';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = ['question', 'answer'];

    protected $fillable = [
        'faq_category_id',
        'question',
        'answer',
        'status',
    ];

    protected $casts = [
        'status' => 'enum:active,inactive',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Translatable\HasTranslations;

class FaqCategory extends Model
{
    use HasFactory, Notifiable, SoftDeletes, HasTranslations;

    protected $table = 'faq_categories';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'faq_category_id');
    }
}

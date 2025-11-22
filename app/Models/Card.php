<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class Card extends Model
{
    use HasRoles, SoftDeletes, Notifiable, HasTranslations;

    protected $table = 'cards';
    public $guard_name = 'admin';
    protected $guarded = [];

    public $translatable = [
        'title',
        'description',
        'link_text'
    ];

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

    // --- TRANSLATION ACCESSORS ---

    public function getTitleTranslatedAttribute()
    {
        return gtrans($this->title);
    }

    public function getDescriptionTranslatedAttribute()
    {
        return gtrans($this->description);
    }

    public function getLinkTextTranslatedAttribute()
    {
        return gtrans($this->link_text);
    }
}

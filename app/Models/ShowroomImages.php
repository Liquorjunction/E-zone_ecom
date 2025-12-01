<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowroomImages extends Model
{
    protected $table = 'showroom_images';
    public $guard_name = 'admin';

    protected $fillable = [
        'showroom_id',
        'image',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function showroom()
    {
        return $this->belongsTo(Showrooms::class);
    }
}

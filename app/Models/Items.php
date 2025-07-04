<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price',
        'image_url',
        'category',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

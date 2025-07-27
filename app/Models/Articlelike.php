<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleLike extends Model
{
    protected $fillable = [
        'likeable_type',
        'likeable_id',
        'user_id',
        'admin_id',

    ];
}

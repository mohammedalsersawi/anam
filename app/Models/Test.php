<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Test extends Model
{
    use HasFactory, HasTranslations;
    const PATH_IMAGE = "/upload/Tests/";
    protected $hidden = ['created_at', 'updated_at'];
    protected $translatable = ['title', 'description',];
    protected $fillable = [
        'title',
        'description',
        'price',
        'rating',
        'questions_count',
        'age_from',
        'age_to',
        'status',
        'rating_count',
        'category_id',
    ];
}

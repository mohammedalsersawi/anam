<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use HasFactory, HasTranslations;
    const PATH_IMAGE = "/upload/Course/";
    protected $hidden = ['created_at', 'updated_at'];
    protected $translatable = ['title', 'description',];
    protected $fillable = [
        'title',
        'description',
        'price',
        'rating',
        'rating_count',
        'hours',
        'status',
        'age_from',
        'age_to',
        'category_id',
    ];


    // In Course.php model
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function image()
    {
        return $this->morphOne(Upload::class, 'relation')->select('id', 'path', 'relation_id', 'relation_type');
    }
}

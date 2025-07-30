<?php

namespace App\Models;

use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'created_by',
        'updated_by',
    ];

    public function image()
    {
        return $this->morphOne(Upload::class, 'relation')->select('id', 'path', 'relation_id', 'relation_type');
    }
}

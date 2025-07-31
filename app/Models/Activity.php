<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory, HasTranslations;
    const PATH_IMAGE = "/upload/Activity/";

    protected $fillable = [
        'title',
        'description',
        'facebook_url',
        'instagram_url',
        'button_text',
        'status',
        'created_by',
        'updated_by',
    ];
    protected $translatable = [
        'title',
        'description',
        'facebook_url',
        'instagram_url',
        'button_text',
    ];

    public function image()
    {
        return $this->morphOne(Upload::class, 'relation')->select('id', 'path', 'relation_id', 'relation_type');
    }
}

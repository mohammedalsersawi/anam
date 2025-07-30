<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SuccessStorie extends Model
{
    use HasFactory, HasTranslations;
    protected $translatable = ['title'];
    protected $fillable = ['title', 'url_video' , 'created_by' , 'updated_by'];
    const PATH_IMAGE = "/upload/SuccessStorie/";
    const PATH_VIDEO = "/upload/SuccessStorie/";
    protected $hidden = ['created_at', 'updated_at'];
    public function images()
    {
        return $this->morphMany(Upload::class, 'relation')->select('id', 'path', 'relation_id', 'relation_type' , 'full_original_path');
    }
}

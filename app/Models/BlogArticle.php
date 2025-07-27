<?php

namespace App\Models;

use App\Models\Upload;
use App\Models\BlogInteraction;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogArticle extends Model
{
    use HasFactory, HasTranslations;

    const PATH_IMAGE = "/upload/Blog/Article";

    protected $hidden = ['created_at', 'updated_at'];

    protected $translatable = ['title', 'excerpt', 'content', 'meta_title', 'meta_description' ,'slug'];

    protected $fillable = [
        'title',
        'excerpt',
        'content',
        'blog_category_id',
        'created_by',
        'status',
        'meta_description',
        'meta_title',
        'slug',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function interactions()
    {
        return $this->hasMany(BlogInteraction::class);
    }

    public function comments()
    {
        return $this->interactions()->where('type', 'comment');
    }

    public function likes()
    {
        return $this->interactions()->where('type', 'like');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

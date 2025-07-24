<?php

namespace App\Models;

use App\Models\Upload;
use App\Models\Feature;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceSection extends Model
{
    use HasFactory, HasTranslations;
    protected $translatable = [
        'title',
        'description',
        'button_text',
    ];
    protected $fillable = [
        'title',
        'description',
        'button_text',
        'button_link',
        'status',
        'icon',
    ];
    const PATH_IMAGE = "/upload/ Services/";

    protected $hidden = ['created_at', 'updated_at'];

    public function features()
    {
        return $this->morphMany(Feature::class, 'section');
    }

     public function image()
    {
        return $this->morphOne(Upload::class, 'relation')->select('id', 'path', 'relation_id', 'relation_type');
    }
}

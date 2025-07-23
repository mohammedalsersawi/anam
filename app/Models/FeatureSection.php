<?php

namespace App\Models;

use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeatureSection extends Model
{
        use HasFactory, HasTranslations;

       protected $fillable = [
        'title',
        'status'
    ];
    protected $translatable = ['title'];

    const PATH_IMAGE = "/upload/Features/";

}

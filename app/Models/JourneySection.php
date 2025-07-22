<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class JourneySection extends Model
{
    use HasFactory, HasTranslations;
      protected $translatable = [
        'title',
        'description',
    ];
      protected $fillable = [
        'title',
        'description',
    ];

        const PATH_IMAGE = "/upload/JourneySection/";

}

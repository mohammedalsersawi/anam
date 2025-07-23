<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

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
}

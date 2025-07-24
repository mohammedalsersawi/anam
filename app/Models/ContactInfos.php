<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactInfos extends Model
{
    use HasFactory, HasTranslations;

    protected $translatable = ['title', 'sub_title', 'description', 'sub_description', 'address'];
    protected $hidden = ['created_at', 'updated_at'];
    const PATH_IMAGE = "/upload/ContactInfos/";

    protected $fillable = [
        'title',
        'sub_title',
        'description',
        'sub_description',
        'address',
        'email',
        'phone',
        'phone_alt',
        'whatsapp',
        'facebook',
        'instagram',
        'youtube',
    ];
}

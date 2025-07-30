<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactInfo extends Model
{
    use HasFactory, HasTranslations;

    protected $translatable = ['title', 'sub_title', 'description', 'sub_description', 'address'];
    protected $hidden = ['created_at', 'updated_at'];
    const PATH_IMAGE = "/upload/ContactInfo/";

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
        'created_by',
        'updated_by',
    ];
    public function images()
    {
        return $this->morphOne(Upload::class, 'relation')->select('id', 'path', 'relation_id', 'relation_type');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = ['name', 'slug', 'description', 'status'];
    protected $translatable = ['name', 'description', 'slug'];
}

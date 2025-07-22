<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NavigationLink extends Model
{
    use HasFactory , HasTranslations ;

    protected $translatable = ['label'];

    protected $fillable = [
        'label',
        'url',
        'position',
        'status',
        'order',
    ];
}

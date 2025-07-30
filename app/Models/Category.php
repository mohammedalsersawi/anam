<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory, HasTranslations;
    protected $translatable = ['name'];
    protected $fillable = [
        'name',
        'status',
        'type',
        'created_by',
        'updated_by',
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'name' => 'array',
    ];
}

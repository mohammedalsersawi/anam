<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class JourneyFeature extends Model
{
    use HasFactory, HasTranslations;

    protected $translatable = ['feature'];
    protected $fillable = ['feature', 'journey_section_id'];
    protected $hidden = ['created_at', 'updated_at'];
}

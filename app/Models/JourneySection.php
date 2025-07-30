<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class JourneySection extends Model
{
    use HasFactory, HasTranslations;
    protected $translatable = ['title','description'];
    protected $fillable = ['title','description' , 'created_by','updated_by'];
    protected $hidden = ['created_at', 'updated_at'];

    const PATH_IMAGE = "/upload/JourneySection/";


    public function images()
    {
        return $this->morphMany(Upload::class, 'relation');
    }

    public function features()
    {
        return $this->hasMany(JourneyFeature::class, 'journey_section_id');
    }
}

<?php

namespace App\Models;

use App\Models\Upload;
use App\Models\Feature;
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
    protected $hidden = ['created_at', 'updated_at'];

    const PATH_IMAGE = "/upload/Features/";
    public function features()
    {
        return $this->morphMany(Feature::class, 'section');
    }
    public function image()
    {
        return $this->morphOne(Upload::class, 'relation')->select('id', 'path', 'relation_id', 'relation_type');
    }

    public function formatForApi()
    {
        $locale = app()->getLocale();

        return [
            'sub_title'   => $this->getTranslation('sub_title', $locale),
            'description' => $this->getTranslation('description', $locale),
            'icon'        => $this->icon,
            'id'        => $this->id,
        ];
    }
}

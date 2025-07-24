<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Feature extends Model
{
    use HasTranslations;

    protected $fillable = [
        'section_id',
        'section_type',
        'sub_title',
        'description',
        'icon',
        'button_text',
        'button_link',
    ];

    public $translatable = ['sub_title', 'description', 'button_text'];
    protected $hidden = ['created_at', 'updated_at'];

    public function section()
    {
        return $this->morphTo();
    }
}

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


    public function formatForApi()
{
    $locale = app()->getLocale();

    // نتحقق إذا العلاقة محملة
    $sectionType = $this->section_type;

    $data = [
        'sub_title'   => $this->getTranslation('sub_title', $locale),
        'description' => $this->getTranslation('description', $locale),
        'icon'        => $this->icon,
        'id'          => $this->id,
    ];

    // نضيف button_text فقط إذا كانت من ServiceSection
    if ($sectionType === \App\Models\ServiceSection::class) {
        $data['button_text'] = $this->getTranslation('button_text', $locale);
    }

    return $data;
}

}

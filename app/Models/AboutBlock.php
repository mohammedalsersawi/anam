<?php

namespace App\Models;

use App\Models\AboutItem;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AboutBlock extends Model
{
    use HasTranslations;
    const PATH_IMAGE = "/upload/AboutBlock/";

    protected $fillable = ['type', 'title', 'description'];

    public $translatable = ['title', 'description'];

    public function items()
    {
        return $this->hasMany(AboutItem::class);
    }
}


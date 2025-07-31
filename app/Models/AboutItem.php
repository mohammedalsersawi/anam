<?php

namespace App\Models;

use App\Models\AboutBlock;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AboutItem extends Model
{
    use HasTranslations;

    protected $fillable = ['about_block_id', 'text'];

    public $translatable = ['text'];

    public function block()
    {
        return $this->belongsTo(AboutBlock::class, 'about_block_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Setting extends Model
{

    use HasTranslations;
    protected $fillable = [
        'key',
        'value',
        'text',
        'created_by',
        'updated_by',
    ];
    public $translatable = ['text' ,'value'];
        protected $hidden = ['created_at', 'updated_at' , 'created_by' , 'updated_by'];

}

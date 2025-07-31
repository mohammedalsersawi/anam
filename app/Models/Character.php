<?php

namespace App\Models;

use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Character extends Model
{
    use HasTranslations;
    const PATH_IMAGE = "/upload/Character/";

    protected $fillable = ['name', 'description', 'status', 'updated_by', 'created_by'];
    protected $hidden = ['created_at', 'updated_at' ,'updated_by' ,'created_by'];

    public $translatable = ['name', 'description'];
    public function image()
    {
        return $this->morphOne(Upload::class, 'relation')->select('id', 'path', 'full_original_path', 'relation_id', 'relation_type');
    }
}

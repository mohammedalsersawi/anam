<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Upload extends Model
{
    use HasFactory;

    public function relation()
{
    return $this->morphTo(__FUNCTION__, 'relation_type', 'relation_id');
}

    protected $guarded = [];
    protected $hidden = ['type', 'created_at', 'updated_at' , 'relation_id' , 'relation_type' ,'extension' , 'filename' , 'name'];

    const IMAGE = 1;
    const VIDEO = 2;
    const VOICE = 3;
    const LOCATION = 4;
    const ATTACHMENT = 4;
}

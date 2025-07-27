<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogInteraction extends Model
{
    const TYPE_LIKE = 1;
    const TYPE_COMMENT = 2;
}

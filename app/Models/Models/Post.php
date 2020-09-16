<?php

namespace App\Models\Models;

use App\Traits\HasParent;

class Post extends Model
{
    use HasParent;

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}

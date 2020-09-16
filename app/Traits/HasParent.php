<?php

namespace App\Traits;

trait HasParent
{
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}

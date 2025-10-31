<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $fillable = [
        'title',
        'description',
        'year_created',
        'artist_id',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
}

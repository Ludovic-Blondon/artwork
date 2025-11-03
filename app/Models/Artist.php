<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $fillable = [
        'name',
        'bio',
        'birth_date',
        'death_date',
    ];

    public function works()
    {
        return $this->hasMany(Work::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = [
        'campus_id',
        'name',
        'map',
    ];

    public function buildings()
    {
        return $this->hasMany(Building::class, 'campus_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = [
        'name',
        'map',
    ];

    public $timestamps = false;

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }
}

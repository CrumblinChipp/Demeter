<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = [
        'campus_id',
        'name',
        'map_x_percent',
        'map_y_percent',
    ];

    public $timestamps = false;
}

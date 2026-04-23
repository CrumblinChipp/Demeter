<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = [
        'building_id',
        'name',
        'status',
        'current_weight',
    ];
    public function buildings()
    {
        return $this->hasMany(Building::class);
    }
}

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

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function wasteEntries()
    {
        return $this->hasMany(WasteEntry::class);
    }
}

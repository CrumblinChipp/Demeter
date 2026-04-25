<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bin extends Model
{
    use HasFactory;

    protected $table = 'smart_bins';
    protected $primaryKey = 'bin_id';

    protected $fillable = [
        'building_id',
        'name',
        'waste_type',
        'status',
        'current_weight',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

}
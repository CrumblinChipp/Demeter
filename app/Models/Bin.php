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
        'capacity',
        'installed_at',
    ];

    protected $casts = [
        'installed_at' => 'datetime',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get waste entries for this bin's building.
     * Since waste entries are per-building (not per-bin),
     * this returns entries from the same building.
     */
    public function wasteEntries()
    {
        return $this->hasMany(WasteEntry::class, 'building_id', 'building_id');
    }
}
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
        'device_key',
        'status',
        'current_weight',
        'capacity',
        'installed_at',
    ];

    protected $casts = [
        'installed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-calculate status from weight/capacity whenever bin is saved
        static::saving(function ($bin) {
            if ($bin->capacity > 0) {
                $bin->status = min(100, round(($bin->current_weight / $bin->capacity) * 100));
            }
        });
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
    public function wasteEntries()
    {
        return $this->belongsToMany(WasteEntry::class, 'pivot', 'bin_id', 'waste_entry_id')
                    ->withPivot('weight', 'entry_date');
    }

}
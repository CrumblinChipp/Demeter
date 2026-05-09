<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bin extends Model
{
    use HasFactory;

    protected $table = 'smart_bins';
    protected $primaryKey = 'bin_id';

    public $timestamps = false;
    protected $fillable = [
        'building_id',
        'name',
        'waste_type',
        'device_key',
        'status',
        'current_weight',
        'capacity',
        'installed_at',
        'is_registered',
        'is_detected',
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

        // Listen for when the weight drops to 0 and automatically record the collection
        static::updating(function ($bin) {
            // Check if weight is being changed to 0, AND it was previously greater than 0
            if ($bin->isDirty('current_weight') && $bin->current_weight <= 0) {
                $oldWeight = $bin->getOriginal('current_weight');

                if ($oldWeight > 0) {
                    $wasteColumn = match (strtolower($bin->waste_type)) {
                        'biodegradable' => 'biodegradable_kg',
                        'recyclable'    => 'recyclable_kg',
                        'residual'      => 'residual_kg',
                        'infectious'    => 'infectious_kg',
                        default         => null,
                    };

                    if ($wasteColumn) {
                        // Find today's record for this building, or create a fresh one if it's the first collection today
                        $entry = \App\Models\WasteEntry::firstOrCreate(
                            ['date' => now()->toDateString(), 'building_id' => $bin->building_id],
                            [
                                'biodegradable_kg' => 0,
                                'recyclable_kg'    => 0,
                                'residual_kg'      => 0,
                                'infectious_kg'    => 0,
                            ]
                        );

                        // Add the collected weight to the running total for today
                        $entry->increment($wasteColumn, $oldWeight);

                        // ALSO log this exact individual collection event in the Pivot table
                        // Uses the remote's pivot structure with waste_entry_id, bin_id, weight, entry_date
                        \Illuminate\Support\Facades\DB::table('pivot')->insert([
                            'waste_entry_id' => $entry->id,
                            'bin_id'         => $bin->bin_id,
                            'weight'         => $oldWeight,
                            'entry_date'     => now()->toDateString(),
                        ]);
                    }
                }
            }
        });
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get waste entries through the pivot table (belongsToMany from remote).
     * Used by bin.blade.php to show weight history with dates.
     */
    public function wasteEntries()
    {
        return $this->belongsToMany(WasteEntry::class, 'pivot', 'bin_id', 'waste_entry_id')
                    ->withPivot('weight', 'entry_date');
    }

    /**
     * Get individual collection events for this specific bin from the pivot table.
     * Direct hasMany for simpler queries when you just need raw collection logs.
     */
    public function collections()
    {
        return $this->hasMany(Pivot::class, 'bin_id', 'bin_id');
    }
}
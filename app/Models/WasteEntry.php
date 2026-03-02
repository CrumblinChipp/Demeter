<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteEntry extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'building_id',
        'residual_kg',
        'recyclable_kg',
        'biodegradable_kg',
        'infectious_kg',
    ];

    public $timestamps = false;

    public function scopeForCampus($query, $campusId)
    {
        return $query->whereHas('building', fn($q) => $q->where('campus_id', $campusId));
    }

    public function scopeInDateRange($query, $days)
    {
        return $query->where('date', '>=', now()->subDays($days - 1)->toDateString());
    }

    // Helper to sum all waste types in SQL
    public function scopeSelectTotalWaste($query)
    {
        // Change these to match your actual database columns (e.g., adding _kg)
        return $query->selectRaw('SUM(residual_kg + recyclable_kg + biodegradable_kg + infectious_kg) as total');
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}

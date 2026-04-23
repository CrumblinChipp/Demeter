<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\WasteEntry;
use App\Models\Building;
use Carbon\Carbon;

class DashboardController
{
public function getStats(Request $request, $campusId) {
    $days = $request->input('days', 7);
    $baseQuery = WasteEntry::forCampus($campusId)->inDateRange($days);

    // 2. Base Query (Reused for all stats)
    $baseQuery = WasteEntry::forCampus($campusId)->inDateRange($days);

    // 3. Overall Waste Per Day (For the main chart)
    $dailyTotals = (clone $baseQuery)
        ->selectRaw('date, SUM(residual_kg + recyclable_kg + biodegradable_kg + infectious_kg) as total')
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('total', 'date');

    // 4. Summary Stats (Highest, Lowest, Average)
    $totalsArray = $dailyTotals->values()->toArray();
    $summary = [
        'highest' => $dailyTotals->max(),
        'highest_date' => $dailyTotals->search($dailyTotals->max()),
        'lowest'  => $dailyTotals->min(),
        'average' => count($totalsArray) ? round(array_sum($totalsArray) / count($totalsArray), 2) : 0
    ];

    // 5. Waste Per Type (Composition)
    $composition = (clone $baseQuery)
        ->selectRaw('SUM(biodegradable_kg) as biodegradable, SUM(residual_kg) as residual, 
                    SUM(recyclable_kg) as recyclable, SUM(infectious_kg) as infectious')
        ->first();

    // 6. Waste Per Building (Bar Chart total)
    $perBuilding = (clone $baseQuery)
        ->join('buildings', 'waste_entries.building_id', '=', 'buildings.id')
        ->selectRaw('buildings.name, SUM(residual_kg + recyclable_kg + biodegradable_kg + infectious_kg) as total')
        ->groupBy('buildings.name')
        ->pluck('total', 'name');

    // 6. Waste Per Building (Stacked Bar Chart Data)
    $perBuildingWaste = (clone $baseQuery)
        ->join('buildings', 'waste_entries.building_id', '=', 'buildings.id')
        ->selectRaw('
            buildings.name, 
            SUM(biodegradable_kg) as bio, 
            SUM(residual_kg) as res, 
            SUM(recyclable_kg) as rec, 
            SUM(infectious_kg) as inf,
            SUM(residual_kg + recyclable_kg + biodegradable_kg + infectious_kg) as total
        ')
        ->groupBy('buildings.name')
        ->get()
        ->keyBy('name'); // This makes it easier to loop through in JS

    return [
        'dailyLabels'     => $dailyTotals->keys(),
        'dailyValues'     => $dailyTotals->values(),
        'summary'         => $summary,
        'composition'     => $composition,
        'buildingTotals'  => $perBuilding,
        'buildingWaste'   => $perBuildingWaste,
        'selectedCampus'  => $campusId,
        'selectedDays'    => $days,
        'campuses'        => Campus::all()
    ];
}

/* -----------------------------------------------------
 * API METHODS FOR MARKER CRUD
 * --------------------------------------------------- */
    public function updateBuildingCoordinates(Request $request, $buildingId)
    {
        $building = Building::findOrFail($buildingId);

        $validated = $request->validate([
            'map_x_percent' => 'nullable|numeric|between:0,100', 
            'map_y_percent' => 'nullable|numeric|between:0,100',
            '_method' => 'required|in:PUT', 
        ]);
        
        $building->update([
            'map_x_percent' => $validated['map_x_percent'],
            'map_y_percent' => $validated['map_y_percent'],
        ]);

        return response()->json($building);
    }

}
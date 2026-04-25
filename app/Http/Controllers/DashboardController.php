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
            ->keyBy('name');

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

}
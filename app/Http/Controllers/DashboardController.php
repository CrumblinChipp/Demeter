<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\WasteEntry;
use App\Models\Building;
use App\Models\Bin;
use Carbon\Carbon;

class DashboardController
{
    public function getStats(Request $request, $campusId) {
        $days = $request->input('days', 7);

        // Base Query (Reused for all stats)
        $baseQuery = WasteEntry::forCampus($campusId)->inDateRange($days);

        // 3. Overall Waste Per Day (For the main chart)
        $dailyTotals = (clone $baseQuery)
            ->selectRaw('date, SUM(residual_kg + recyclable_kg + biodegradable_kg + infectious_kg) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // 4. Summary Stats (Highest, Lowest, Average)
        $totalsArray = $dailyTotals->values()->toArray();
        
        $highest = $dailyTotals->max() ?? 0;
        $lowest = $dailyTotals->isEmpty() ? 0 : $dailyTotals->min();
        $highestDate = $highest > 0 ? $dailyTotals->search($highest) : 'N/A';
        
        $summary = [
            'highest' => $highest,
            'highest_date' => $highestDate,
            'lowest'  => $lowest,
            'average' => count($totalsArray) ? round(array_sum($totalsArray) / count($totalsArray), 2) : 0
        ];

        // 5. Waste Per Type (Composition)
        $composition = (clone $baseQuery)
            ->selectRaw('SUM(biodegradable_kg) as biodegradable, SUM(residual_kg) as residual, 
                        SUM(recyclable_kg) as recyclable, SUM(infectious_kg) as infectious')
            ->first();

        // Get all buildings for this campus to ensure they all appear in the charts (even with 0 waste)
        $buildings = Building::where('campus_id', $campusId)->get();
        $buildingNames = $buildings->pluck('name');

        // 6. Waste Per Building (Bar Chart total)
        $perBuildingWasteData = (clone $baseQuery)
            ->selectRaw('
                building_id,
                SUM(biodegradable_kg) as bio, 
                SUM(residual_kg) as res, 
                SUM(recyclable_kg) as rec, 
                SUM(infectious_kg) as inf,
                SUM(residual_kg + recyclable_kg + biodegradable_kg + infectious_kg) as total
            ')
            ->groupBy('building_id')
            ->get()
            ->keyBy('building_id');

        $perBuilding = collect();
        $perBuildingWaste = collect();

        foreach ($buildings as $b) {
            $data = $perBuildingWasteData->get($b->id);
            
            $perBuilding[$b->name] = $data ? (float)$data->total : 0;
            
            $perBuildingWaste[$b->name] = [
                'name'  => $b->name,
                'bio'   => $data ? (float)$data->bio : 0,
                'res'   => $data ? (float)$data->res : 0,
                'rec'   => $data ? (float)$data->rec : 0,
                'inf'   => $data ? (float)$data->inf : 0,
                'total' => $data ? (float)$data->total : 0,
            ];
        }

        // Bin Status Overview
        $binStatus = Bin::whereHas('building', fn($q) => $q->where('campus_id', $campusId))
            ->selectRaw("
                COUNT(*) as total_bins,
                SUM(CASE WHEN status < 11 THEN 1 ELSE 0 END) as empty_bins,
                SUM(CASE WHEN status >= 11 AND status < 71 THEN 1 ELSE 0 END) as filled_bins,
                SUM(CASE WHEN status >= 71 THEN 1 ELSE 0 END) as full_bins
            ")
            ->first();

        return [
            'dailyLabels'     => $dailyTotals->keys(),
            'dailyValues'     => $dailyTotals->values(),
            'summary'         => $summary,
            'composition'     => $composition,
            'buildingTotals'  => $perBuilding,
            'buildingWaste'   => $perBuildingWaste,
            'binStatus'       => $binStatus,
            'selectedCampus'  => $campusId,
            'selectedDays'    => $days,
        ];
    }

}
<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Building;

class BuildingController
{
    public function updateCoordinates(Request $request, Building $building)
    {
        $validated = $request->validate([
            'map_x_percent' => 'nullable|numeric|min:0|max:100',
            'map_y_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $building->update($validated);

        return response()->json($building);
    }
}
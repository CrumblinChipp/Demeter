<?php

namespace App\Http\Controllers;

use App\Models\WasteEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WasteEntryController
{
    public function store(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'campus_id'        => 'required|exists:campuses,id',
            'building_id'      => 'required|exists:buildings,id',
            'biodegradable_kg' => 'nullable|numeric|min:0',
            'recyclable_kg'    => 'nullable|numeric|min:0',
            'residual_kg'      => 'nullable|numeric|min:0',
            'infectious_kg'    => 'nullable|numeric|min:0',
        ]);

        try {
            // 2. Create the Entry using Mass Assignment 
            $entry = WasteEntry::create([
                'user_id'          => Auth::id(),
                'date'             => now(),
                'building_id'      => $validated['building_id'],
                'biodegradable_kg' => $validated['biodegradable_kg'] ?? 0,
                'recyclable_kg'    => $validated['recyclable_kg'] ?? 0,
                'residual_kg'      => $validated['residual_kg'] ?? 0,
                'infectious_kg'    => $validated['infectious_kg'] ?? 0,
            ]);

            // 3. Return Response for AJAX
            return response()->json([
                'status' => 'success',
                'message' => 'Waste entry recorded successfully!',
                'data' => $entry
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy(WasteEntry $waste)
    {
        $waste->delete();

        // Redirect back to the data section of the homepage
        return redirect()->route('homepage', ['section' => 'data'])
                        ->with('success', 'Entry deleted successfully');
    }
}
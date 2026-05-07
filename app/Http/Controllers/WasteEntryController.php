<?php

namespace App\Http\Controllers;

use App\Models\WasteEntry;
use App\Models\Bin;
use Illuminate\Http\Request;

class WasteEntryController
{
    public function collect(Request $request)
    {
        $validated = $request->validate([
            'bin_id' => 'required|exists:smart_bins,bin_id',
        ]);

        try {
            $bin = Bin::with('building')->findOrFail($validated['bin_id']);

            // Nothing to collect if bin is already empty
            if ($bin->current_weight <= 0) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Bin is already empty, nothing to collect.'
                ], 200);
            }

            // Map the bin's waste_type to the correct column
            $wasteColumn = match ($bin->waste_type) {
                'Biodegradable' => 'biodegradable_kg',
                'Recyclable'    => 'recyclable_kg',
                'Residual'      => 'residual_kg',
                'Infectious'    => 'infectious_kg',
                default         => null,
            };

            if (!$wasteColumn) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unknown waste type: ' . $bin->waste_type
                ], 400);
            }

            // Create waste entry with the weight under the correct type
            $collectedWeight = $bin->current_weight;

            $entry = WasteEntry::create([
                'date'             => now(),
                'building_id'      => $bin->building_id,
                'biodegradable_kg' => 0,
                'recyclable_kg'    => 0,
                'residual_kg'      => 0,
                'infectious_kg'    => 0,
                $wasteColumn       => $collectedWeight,
            ]);

            // Reset the bin
            $bin->update([
                'current_weight' => 0,
                'status'         => 0,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => "Collected {$collectedWeight}kg of {$bin->waste_type} from {$bin->name}",
                'entry'   => $entry,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Collection failed: ' . $e->getMessage()
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
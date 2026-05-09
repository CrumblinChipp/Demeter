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

            $collectedWeight = $bin->current_weight;

            // Simply reset the bin.
            // The Bin model's 'updating' event will automatically detect this
            // drop to 0kg and create the WasteEntry record for us.
            $bin->update([
                'current_weight' => 0,
                'status'         => 0,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => "Collected {$collectedWeight}kg of {$bin->waste_type} from {$bin->name} (Auto-recorded)",
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
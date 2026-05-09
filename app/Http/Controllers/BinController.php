<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bin;
use App\Models\Building;

class BinController
{
  public function storeBin(Request $request)
  {
      $request->validate([
          'building_id' => 'required|exists:buildings,id',
          'device_key'  => 'required|string|unique:smart_bins,device_key',
          'waste_type'  => 'required|in:biodegradable,recyclable,residual,infectious',
          'capacity'    => 'required|numeric|min:1',
      ]);

      $building = Building::findOrFail($request->building_id);

      // Example:
      // ENGINEERING_RESIDUAL
      $binName = str_replace(' ', '_', $building->name)
          . '_'
          . $request->waste_type;

      Bin::create([
          'building_id'   => $request->building_id,
          'name'          => $binName,
          'device_key'    => $request->device_key,
          'waste_type'    => $request->waste_type,
          'capacity'      => $request->capacity,
          'status'        => 0,
          'current_weight'=> 0,
          'is_registered' => false,
          'installed_at'  => now(),
      ]);

      return redirect()->route('homepage', [
          'section' => 'admin',
          'tab'     => 'add-bin',
          'bin'     => 'unmatched',
          'campus'  => $building->campus_id,
      ])->with('success', 'Bin registered successfully.');
  }
}
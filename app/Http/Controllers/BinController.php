<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bin;

class BinController
{
  public function getBinStatus()
  {
      // Get all bins
      $bins = Bin::all(['name', 'status', 'current_weight', 'waste_type']);

      return response()->json($bins);
  }
}
<?php
use App\Models\Bin;
use App\Models\WasteEntry;

$bin = Bin::first();
echo "Testing Bin ID: " . $bin->bin_id . " Name: " . $bin->name . " Type: " . $bin->waste_type . "\n";
echo "Initial Weight: " . $bin->current_weight . "\n";

$bin->current_weight = 12;
$bin->save();
echo "Set Weight to 12. New DB Weight: " . Bin::find($bin->bin_id)->current_weight . "\n";

$bin->current_weight = 0;
$bin->save();
echo "Set Weight to 0. Final DB Weight: " . Bin::find($bin->bin_id)->current_weight . "\n";

$entry = WasteEntry::orderBy('id', 'desc')->first();
echo "Latest WasteEntry: " . json_encode($entry) . "\n";

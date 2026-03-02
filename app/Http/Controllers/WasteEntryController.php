<?php

namespace App\Http\Controllers;

use App\Models\WasteEntry;
use Illuminate\Http\Request;

class WasteEntryController
{
    public function destroy(WasteEntry $waste)
    {
        $waste->delete();

        // Redirect back to the data section of the homepage
        return redirect()->route('homepage', ['section' => 'data'])
                         ->with('success', 'Entry deleted successfully');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\WasteEntry;
use App\Models\Building;
use App\Models\Bin;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BinController;

class HomeController
{

    public function index(Request $request)
    {
        // Pick up the section from the URL, default to 'dashboard'
        $section = $request->query('section', 'dashboard');

        $campusId = $request->input('campus', 1);

        $data = [
            'currentSection' => $section,
            'campuses' => Campus::all(),
            'selectedCampus' => $request->campus ?? 1,
            'selectedDays' => $request->days ?? 7,
        ];

        if ($section === 'dashboard') {
            $dashboardController = new DashboardController();
            $stats = $dashboardController->getStats($request, $data['selectedCampus']);
            $data = array_merge($data, $stats);
        }
        elseif ($section === 'data') {
            $perPage = $request->input('per_page', 20);
            
            $data['wastes'] = WasteEntry::whereHas('building', function($q) use ($campusId) {
                    $q->where('campus_id', $campusId);
                })
                ->with('building')
                ->orderBy('date', 'desc')
                ->paginate($perPage)
                ->withQueryString();
        }
        elseif ($section === 'map') {
        // 1. Fetch the specific campus
            $data['campus'] = Campus::with(['buildings' => function($query) {
                $query->whereNotNull('map_x_percent')->whereNotNull('map_y_percent');
            }])->find($campusId);

            if (!$data['campus']) {
                $data['campus'] = Campus::first();
            }
        }

        elseif ($section ==='bin') {
            $perPage = $request->input('per_page', 20);

            $data['smart_bins'] = Bin::whereHas('building', function($q) use ($campusId) {
                    $q->where('campus_id', $campusId);
                })
                ->orderBy('name', 'asc')
                ->paginate($perPage)
                ->withQueryString();
        }
        elseif ($section === 'admin') {
            // Default to 'add-campus' if no tab is specified
            $data['activeTab'] = $request->query('tab', 'add-campus');
            $data['campusToEdit'] = Campus::with('buildings')->find($campusId);
            // If your edit-campus or edit-map needs specific data (like a list of campuses), fetch it here:
            $data['allCampuses'] = Campus::all();

            $data['buildings'] = Building::where('campus_id', $campusId)->get();
            $data['campus'] = Campus::find($campusId);

            if (!$data['campusToEdit']) {
                $data['campusToEdit'] = Campus::with('buildings')->first();
            }
        }

        return view('homepage', $data);
    }
}
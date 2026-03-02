<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\WasteEntry;
use App\Models\Building;
use App\Http\Controllers\DashboardController;
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

        // ONLY fetch dashboard math if we are actually on the dashboard
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
                ->with('building') // Eager load to prevent 100+ database queries (N+1)
                ->orderBy('date', 'desc')
                ->paginate($perPage)
                ->withQueryString(); // Keeps ?section=data&campus=1 in pagination links
        }
        elseif ($section === 'map') {
        // 1. Fetch the specific campus
            $data['campus'] = Campus::with(['buildings' => function($query) {
                // Only get buildings that have been placed on the map
                $query->whereNotNull('map_x_percent')->whereNotNull('map_y_percent');
            }])->find($campusId);

            // 2. Safety check: if no campus found, grab the first one
            if (!$data['campus']) {
                $data['campus'] = Campus::first();
            }
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
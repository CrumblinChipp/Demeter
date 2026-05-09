<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WasteEntryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\BinController;


// ── Public Routes ──────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

Route::view('/welcome','welcome');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ── Authenticated Routes ───────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/homepage', [HomeController::class, 'index'])->name('homepage');

    Route::delete('/waste/{waste}', [WasteEntryController::class, 'destroy'])->name('waste.destroy');

    Route::put('/buildings/{building}/coordinates', [BuildingController::class, 'updateCoordinates'])
        ->name('buildings.coordinates.update');

    Route::get('/api/campuses/{campus}/buildings', function ($campusId) {
        return \App\Models\Building::where('campus_id', $campusId)->get();
    });

    // Admin-only routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::put('/campus/{campus}', [CampusController::class, 'update'])->name('campus.update');
        Route::delete('/campus/{campus}', [CampusController::class, 'destroy'])->name('campus.destroy');
        Route::post('/campus', [CampusController::class, 'store'])->name('campus.store');
        Route::post('/bins/register', [BinController::class, 'storeBin'])->name('bins.register');
        Route::put('/bins/update', [BinController::class, 'updateBin'])->name('bins.update');
    });

    // AI Chat — rate limited to 20 requests per minute per user
    Route::post('/api/ai/ask', [\App\Http\Controllers\AiController::class, 'ask'])
        ->middleware('throttle:20,1')
        ->name('ai.ask');
});

// Bin Collection API — IoT device endpoint (no auth, device-key based)
Route::post('/api/bins/collect', [WasteEntryController::class, 'collect'])->name('bins.collect');
<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WasteEntryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\BuildingController;


Route::get('/', function () {
    return view('welcome');
});

Route::view('/welcome','welcome');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/homepage', [HomeController::class, 'index'])->name('homepage');

Route::delete('/waste/{waste}', [WasteEntryController::class, 'destroy'])->name('waste.destroy');

Route::post('/waste/store', [WasteEntryController::class, 'store'])->name('waste.store');

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Update Campus (Handles the name, map upload, and buildings)
    Route::put('/campus/{campus}', [CampusController::class, 'update'])->name('campus.update');
    
    // Delete Campus
    Route::delete('/campus/{campus}', [CampusController::class, 'destroy'])->name('campus.destroy');

    // Add Campus
    Route::post('/campus', [CampusController::class, 'store'])->name('campus.store');
});

Route::put('/buildings/{building}/coordinates', [BuildingController::class, 'updateCoordinates'])
    ->name('buildings.coordinates.update');

Route::get('/api/campuses/{campus}/buildings', function ($campusId) {
    return \App\Models\Building::where('campus_id', $campusId)->get();
});
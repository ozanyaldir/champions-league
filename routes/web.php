<?php

use App\Http\Controllers\FixtureController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

// HOME
Route::get('/', function () {
    return redirect()->route('teams.index');
});

// TEAMS
Route::prefix('teams')->name('teams.')->group(function () {
    Route::get('/', [TeamController::class, 'index'])->name('index');
});

// FIXTURES
Route::prefix('fixtures')->name('fixtures.')->group(function () {
    Route::get('/', [FixtureController::class, 'index'])->name('index');
    Route::post('/generate', [FixtureController::class, 'generate'])->name('generate');
});

// SIMULATION
Route::prefix('simulation')->name('simulation.')->group(function () {
    Route::get('/', [SimulationController::class, 'index'])->name('index');
    Route::post('/start', [SimulationController::class, 'start'])->name('start');
    Route::post('/play-all', [SimulationController::class, 'playAll'])->name('playAll');
    Route::post('/play-next-week', [SimulationController::class, 'playNextWeek'])->name('playNextWeek');
    Route::post('/reset', [SimulationController::class, 'reset'])->name('reset');
});

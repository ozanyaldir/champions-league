<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\SimulationController;

// HOME
Route::get('/', function () {
    return redirect()->route('teams.index');
});

// TEAMS
Route::get('/teams', [TeamController::class, 'index'])
    ->name('teams.index');

// FIXTURES
Route::get('/fixtures', [FixtureController::class, 'index'])
    ->name('fixtures.index');

Route::post('/fixtures/generate', [FixtureController::class, 'generate'])
    ->name('fixtures.generate');

// SIMULATIONS
Route::post('/simulation/start', [SimulationController::class, 'start'])
    ->name('simulation.start');

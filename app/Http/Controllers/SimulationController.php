<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimulationController extends Controller
{
    public function start(Request $request)
    {
        // Empty for now
        return redirect()->back()->with('success', 'Simulation started (placeholder)');
    }
}

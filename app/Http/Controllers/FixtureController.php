<?php

namespace App\Http\Controllers;

use App\Services\FixtureService;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    protected $fixtureService;

    public function __construct(FixtureService $fixtureService)
    {
        $this->fixtureService = $fixtureService;
    }

    public function index()
    {
        $weeks = $this->fixtureService->getFixturesGroupedByWeek();
        $teams = $this->fixtureService->getAllTeams();

        return view('fixtures', compact('weeks', 'teams'));
    }

    public function generate(Request $request)
    {
        $success = $this->fixtureService->generateFixtures();

        if (!$success) {
            return back()->with('error', 'Not enough teams to generate fixtures.');
        }

        return redirect()->route('fixtures.index');
    }
}

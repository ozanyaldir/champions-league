<?php

namespace App\Http\Controllers;

use App\Services\FixtureService;
use App\Services\TeamService;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    protected $teamService;

    protected $fixtureService;

    public function __construct(TeamService $teamService, FixtureService $fixtureService)
    {
        $this->teamService = $teamService;
        $this->fixtureService = $fixtureService;
    }

    public function index()
    {
        $weeks = $this->fixtureService->getFixturesGroupedByWeek();
        $teams = $this->teamService->getAllTeams();

        return view('fixtures', compact('weeks', 'teams'));
    }

    public function generate(Request $request)
    {
        $this->fixtureService->generateFixtures();

        return redirect()->route('fixtures.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\FixtureService;
use App\Services\SimulationService;
use App\Services\TeamService;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    protected $teamService;
    protected $fixtureService;
    protected $simulationService;

    public function __construct(TeamService $teamService, FixtureService $fixtureService, SimulationService $simulationService)
    {
        $this->teamService = $teamService;
        $this->fixtureService = $fixtureService;
        $this->simulationService = $simulationService;
    }
    
    public function index()
    {
        $teams = $this->teamService->getAll();
        $weeks = $this->fixtureService->getFixturesGroupedByWeek();

        // Build initial empty league table
        $table = collect($teams)->map(function ($team) {
            return [
                'team'    => $team->name,
                'played'  => 0,
                'won'     => 0,
                'draw'    => 0,
                'lost'    => 0,
                'points'  => 0,
            ];
        })->toArray();

        // Determine current week
        $currentWeek = !empty($weeks)
            ? min(array_keys($weeks))
            : null;

        $matches = $currentWeek
            ? $weeks[$currentWeek]
            : [];

        // Temporary equal-weight predictions
        $predictions = [];
        $teamCount = count($teams);

        foreach ($teams as $team) {
            $predictions[$team->name] = $teamCount > 0
                ? 100 / $teamCount
                : 0;
        }

        return view('simulation', compact(
            'table',
            'currentWeek',
            'matches',
            'predictions'
        ));
    }


    public function start(Request $request)
    {
        return redirect()->route('simulation.index');
    }

    public function playAll(Request $request)
    {
        $this->simulationService->playAll();
        return redirect()->route('simulation.index');
    }

    public function playNextWeek(Request $request)
    {
        $this->simulationService->playNextWeek();
        return redirect()->route('simulation.index');
    }

    public function reset(Request $request)
    {
        $this->simulationService->reset();
        return redirect()->route('teams.index');
    }
}

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
        $teamsWithStats = $this->simulationService->buildLeagueTable();
        $table = $teamsWithStats->map(function ($t) {
            return [
                'team' => $t->name,
                'played' => $t->played,
                'won' => $t->won,
                'draw' => $t->draw,
                'lost' => $t->lost,
                'points' => $t->points,
            ];
        })->toArray();

        // $currentWeek = ... // you can compute first unplayed week by checking fixtures/games
        // $matches = ... // fixtures for current week as array of ['home' => name, 'away' => name]
        $currentWeek = 1;
        $matches = [];

        $predictions = $this->simulationService->predictChampionship();

        return view('simulation', compact('table', 'currentWeek', 'matches', 'predictions'));
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

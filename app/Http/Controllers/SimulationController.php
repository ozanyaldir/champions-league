<?php

namespace App\Http\Controllers;

use App\Orchestrators\SimulationOrchestrator;
use App\Services\FixtureService;
use App\Services\TeamService;

class SimulationController extends Controller
{
    protected $teamService;

    protected $fixtureService;

    protected $simulationOrchestrator;

    public function __construct(TeamService $teamService, FixtureService $fixtureService, SimulationOrchestrator $simulationOrchestrator)
    {
        $this->teamService = $teamService;
        $this->fixtureService = $fixtureService;
        $this->simulationOrchestrator = $simulationOrchestrator;
    }

    public function index()
    {
        $table = $this->simulationOrchestrator->buildLeagueTable();

        // $currentWeek = ... // you can compute first unplayed week by checking fixtures/games
        // $matches = ... // fixtures for current week as array of ['home' => name, 'away' => name]
        $currentWeek = 1;
        $matches = [];

        $predictions = $this->simulationOrchestrator->predictChampionship();

        return view('simulation', compact('table', 'currentWeek', 'matches', 'predictions'));
    }

    public function start()
    {
        return redirect()->route('simulation.index');
    }

    public function playAll()
    {
        $this->simulationOrchestrator->playAll();

        return redirect()->route('simulation.index');
    }

    public function playNextWeek()
    {
        $this->simulationOrchestrator->playNextWeek();

        return redirect()->route('simulation.index');
    }

    public function reset()
    {
        $this->simulationOrchestrator->reset();

        return redirect()->route('teams.index');
    }
}

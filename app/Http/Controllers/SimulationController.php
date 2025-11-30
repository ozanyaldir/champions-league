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
        $currentWeekFixtures = $this->simulationOrchestrator->getCurrentWeekFixtures();
        $predictions = $this->simulationOrchestrator->predictChampionship();

        return view('simulation', compact('table', 'currentWeekFixtures', 'predictions'));
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

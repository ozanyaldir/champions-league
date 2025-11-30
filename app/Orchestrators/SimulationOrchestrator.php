<?php

namespace App\Orchestrators;

use App\Services\ChampionshipPredictorService;
use App\Services\FixtureService;
use App\Services\LeagueTableBuilderService;
use App\Services\SimulationService;
use App\Models\Fixture;
use Illuminate\Database\Eloquent\Collection;

class SimulationOrchestrator
{
    protected $fixtureService;

    protected $simulationService;

    protected $championshipPredictorService;

    protected $leagueTableBuilderService;

    public function __construct(
        FixtureService $fixtureService,
        SimulationService $simulationService,
        ChampionshipPredictorService $championshipPredictorService,
        LeagueTableBuilderService $leagueTableBuilderService,
    ) {
        $this->fixtureService = $fixtureService;
        $this->simulationService = $simulationService;
        $this->championshipPredictorService = $championshipPredictorService;
        $this->leagueTableBuilderService = $leagueTableBuilderService;
    }

    public function reset(): void
    {
        $this->simulationService->reset();
    }

    public function playAll(): void
    {
        $this->simulationService->playAll();
    }

    public function playNextWeek(): ?int
    {
        return $this->simulationService->playNextWeek();
    }

    public function buildLeagueTable(): Collection
    {
        return $this->leagueTableBuilderService->buildLeagueTable();
    }

    /**
     * @return array<int, array<int, Fixture>>
     */
    public function getCurrentWeekFixtures(): array
    {
        return $this->fixtureService->getCurrentWeekFixtures();
    }

    public function predictChampionship(): array
    {
        return $this->championshipPredictorService->predictChampionship();
    }
}

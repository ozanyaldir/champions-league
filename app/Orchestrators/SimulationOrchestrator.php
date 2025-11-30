<?php

namespace App\Orchestrators;

use App\Services\ChampionshipPredictorService;
use App\Services\LeagueTableBuilderService;
use App\Services\SimulationService;
use Illuminate\Database\Eloquent\Collection;

class SimulationOrchestrator
{
    protected $simulationService;

    protected $championshipPredictorService;

    protected $leagueTableBuilderService;

    public function __construct(
        SimulationService $simulationService,
        ChampionshipPredictorService $championshipPredictorService,
        LeagueTableBuilderService $leagueTableBuilderService,
    ) {
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

    public function predictChampionship(): array
    {
        return $this->championshipPredictorService->predictChampionship();
    }
}

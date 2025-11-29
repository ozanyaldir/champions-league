<?php

namespace App\Services;

use App\Repositories\FixtureRepository;
use App\Repositories\GameRepository;

class SimulationService
{
    protected $fixtureRepository;

    protected $gameRepository;

    protected $teamRepository;

    public function __construct(
        FixtureRepository $fixtureRepository,
        GameRepository $gameRepository,
    ) {
        $this->fixtureRepository = $fixtureRepository;
        $this->gameRepository = $gameRepository;
    }

    /**
     * @return void
     */
    public function playAll()
    {
        // Implement full simulation logic here
    }

    /**
     * @return void
     */
    public function playNextWeek()
    {
        // Implement next week simulation logic here
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->gameRepository->deleteAll();
        $this->fixtureRepository->deleteAll();
    }
}

<?php

namespace App\Services;

use App\Models\Fixture;
use App\Repositories\FixtureRepository;
use App\Repositories\GameRepository;
use App\Repositories\TeamRepository;

class FixtureService
{
    protected $fixtureRepository;

    protected $gameRepository;

    protected $teamRepository;

    public function __construct(
        FixtureRepository $fixtureRepository,
        GameRepository $gameRepository,
        TeamRepository $teamRepository
    ) {
        $this->fixtureRepository = $fixtureRepository;
        $this->gameRepository = $gameRepository;
        $this->teamRepository = $teamRepository;
    }

    /**
     * @return array<int, array<int, Fixture>>
     */
    public function getFixturesGroupedByWeek(): array
    {
        $fixtures = $this->fixtureRepository->allWithTeams();

        $weeks = [];
        foreach ($fixtures as $fixture) {
            $weeks[$fixture->week][] = $fixture;
        }

        return $weeks;
    }

    /**
     * @return array<int, array<int, Fixture>>
     *                                         Returns all played weeks grouped plus the upcoming week as the last group.
     */
    public function getCurrentWeekFixtures(): array
    {
        $fixtures = $this->fixtureRepository->allWithTeams();

        if ($fixtures->isEmpty()) {
            return [];
        }

        $playedFixtureIds = $this->gameRepository->getPlayedFixtureIds();

        $playedWeeks = [];
        $nextWeekFixtures = [];

        // Group fixtures by played weeks
        foreach ($fixtures as $fixture) {
            if (in_array($fixture->id, $playedFixtureIds, true)) {
                $playedWeeks[$fixture->week][] = $fixture;
            }
        }

        // Determine next week to play
        $allWeeks = $fixtures->pluck('week')->unique()->sort()->values();
        $nextWeek = null;
        foreach ($allWeeks as $week) {
            if (! isset($playedWeeks[$week])) {
                $nextWeek = $week;
                break;
            }
        }

        // Group upcoming fixtures
        if ($nextWeek !== null) {
            foreach ($fixtures as $fixture) {
                if ($fixture->week === $nextWeek) {
                    $nextWeekFixtures[$nextWeek][] = $fixture;
                }
            }
        }

        // Merge played and upcoming, preserving order
        $result = $playedWeeks;
        if (! empty($nextWeekFixtures)) {
            $result[$nextWeek] = $nextWeekFixtures[$nextWeek];
        }

        ksort($result); // sort by week number

        return $result;
    }

    public function generateFixtures(
        int $groupSize = 4,
        int $matchesPerWeek = 2,
        bool $doubleLeg = true
    ) {
        $this->resetSimulationData();

        $teamIds = $this->fetchAndShuffleTeamIds();

        $groups = $this->buildGroups($teamIds, $groupSize);

        $fixtures = $this->generateAllGroupFixtures($groups, $matchesPerWeek, $doubleLeg);

        $this->fixtureRepository->insertMany($fixtures);
    }

    public function resetSimulationData(): void
    {
        $this->gameRepository->deleteAll();
        $this->fixtureRepository->deleteAll();
    }

    protected function fetchAndShuffleTeamIds(): array
    {
        $teams = $this->teamRepository->getAll();
        $teamIds = $teams->pluck('id')->toArray();

        shuffle($teamIds);

        return $teamIds;
    }

    protected function buildGroups(array $teamIds, int $groupSize): array
    {
        $count = count($teamIds);

        if ($count >= $groupSize && $count % $groupSize === 0) {
            return array_chunk($teamIds, $groupSize);
        }

        return [$teamIds];
    }

    protected function generateAllGroupFixtures(
        array $groups,
        int $matchesPerWeek,
        bool $doubleLeg
    ): array {
        $fixtures = [];
        $week = 1;

        foreach ($groups as $groupTeamIds) {

            $groupFixtures = $this->roundRobinFixtures(
                $groupTeamIds,
                $week,
                $doubleLeg,
                $matchesPerWeek
            );

            $fixtures = array_merge($fixtures, $groupFixtures);
            $week = $this->calculateNextGroupWeek($groupFixtures);
        }

        return $fixtures;
    }

    protected function calculateNextGroupWeek(array $fixtures): int
    {
        if (empty($fixtures)) {
            return 1;
        }

        return max(array_column($fixtures, 'week')) + 1;
    }

    protected function roundRobinFixtures(
        array $teamIds,
        int $startWeek = 1,
        bool $doubleLeg = false,
        int $matchesPerWeek = 2
    ): array {

        $teamIds = $this->ensureEvenTeams($teamIds);

        $rounds = $this->generateRoundRobinRounds($teamIds);

        if ($doubleLeg) {
            $rounds = $this->addReverseLegs($rounds);
        }

        return $this->convertRoundsToFixtures($rounds, $startWeek, $matchesPerWeek);
    }

    protected function ensureEvenTeams(array $teamIds): array
    {
        if (count($teamIds) % 2 !== 0) {
            $teamIds[] = null;
        }

        return $teamIds;
    }

    protected function generateRoundRobinRounds(array $teamIds): array
    {
        $n = count($teamIds);
        $roundCount = $n - 1;
        $half = $n / 2;

        $rounds = [];

        for ($round = 0; $round < $roundCount; $round++) {
            $matchday = [];

            for ($i = 0; $i < $half; $i++) {
                $home = $teamIds[$i];
                $away = $teamIds[$n - 1 - $i];

                if ($home !== null && $away !== null) {
                    $matchday[] = [$home, $away];
                }
            }

            $rounds[] = $matchday;

            // MARK: rotate except first
            $last = array_pop($teamIds);
            array_splice($teamIds, 1, 0, [$last]);
        }

        return $rounds;
    }

    protected function addReverseLegs(array $rounds): array
    {
        $reverseRounds = [];

        foreach ($rounds as $matchday) {
            $rev = [];
            foreach ($matchday as [$home, $away]) {
                $rev[] = [$away, $home];
            }
            $reverseRounds[] = $rev;
        }

        return array_merge($rounds, $reverseRounds);
    }

    protected function convertRoundsToFixtures(
        array $rounds,
        int $startWeek,
        int $matchesPerWeek
    ): array {
        $fixtures = [];
        $week = $startWeek;
        $matchesInCurrentWeek = 0;

        foreach ($rounds as $matchday) {

            foreach ($matchday as [$home, $away]) {
                $fixtures[] = [
                    'home_team_id' => $home,
                    'away_team_id' => $away,
                    'week' => $week,
                ];

                $matchesInCurrentWeek++;

                if ($matchesInCurrentWeek >= $matchesPerWeek) {
                    $week++;
                    $matchesInCurrentWeek = 0;
                }
            }

            if ($matchesInCurrentWeek !== 0) {
                $week++;
                $matchesInCurrentWeek = 0;
            }
        }

        return $fixtures;
    }
}

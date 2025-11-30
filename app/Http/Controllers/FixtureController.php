<?php

namespace App\Http\Controllers;

use App\Services\FixtureService;

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

        return view('fixtures', ['weeks' => $weeks]);
    }

    public function generate()
    {
        $this->fixtureService->generateFixtures();

        return redirect()->route('fixtures.index');
    }
}

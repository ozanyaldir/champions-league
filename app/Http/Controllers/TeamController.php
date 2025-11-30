<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamResource;
use App\Services\TeamService;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        $teams = $this->teamService->getAllTeams();
        $teamResources = TeamResource::collection($teams);

        return view('teams', ['teams' => $teamResources]);
    }
}

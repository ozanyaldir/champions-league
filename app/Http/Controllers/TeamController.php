<?php

namespace App\Http\Controllers;

use App\Models\Team;
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

        return view('teams', compact('teams'));
    }
}

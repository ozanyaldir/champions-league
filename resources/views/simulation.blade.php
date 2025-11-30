@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <h2 class="mb-4">Simulation</h2>

    {{-- Horizontal Layout --}}
    <div class="row g-3">

        {{-- League Table --}}
        <div class="col-lg-4 col-md-6">
            <div class="table-responsive rounded-3 shadow-sm" style="max-width: 440px;">
                <table class="table mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Team</th>
                            <th>P</th>
                            <th>W</th>
                            <th>D</th>
                            <th>L</th>
                            <th>Pts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($table as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->played }}</td>
                            <td>{{ $row->won }}</td>
                            <td>{{ $row->draw }}</td>
                            <td>{{ $row->lost }}</td>
                            <td>{{ $row->points }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Current Week Simulation --}}
        @if (!empty($currentWeekFixtures) && count($currentWeekFixtures))
            <div class="col-md-3 mb-4">
                @foreach ($currentWeekFixtures as $weekNumber => $fixtures)
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-dark text-white">
                            Week {{ $weekNumber }}
                        </div>
                        <ul class="list-group list-group-flush">
                            @foreach ($fixtures as $fixture)
                                @php
                                    $game = $fixture->game; // get related game
                                @endphp
                                <li class="list-group-item">
                                    @if($game)
                                        {{ $fixture->homeTeam->name }}({{ $game->home_goals }}) - {{ $fixture->awayTeam->name }}({{ $game->away_goals }})
                                    @else
                                        {{ $fixture->homeTeam->name }} - {{ $fixture->awayTeam->name }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Predictions --}}
        <div class="col-lg-4 col-md-6">
            <div class="table-responsive rounded-3 shadow-sm" style="max-width: 440px;">
                <table class="table mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Team</th>
                            <th>Chance (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($predictions as $team => $percent)
                        <tr>
                            <td>{{ $team }}</td>
                            <td>{{ $percent }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Action Buttons --}}
    <div class="d-flex gap-3 mt-4">
        <form action="{{ route('simulation.playAll') }}" method="POST">@csrf
            <button class="btn btn-success rounded-3">Play All</button>
        </form>

        <form action="{{ route('simulation.playNextWeek') }}" method="POST">@csrf
            <button class="btn btn-primary rounded-3">Play Next Week</button>
        </form>

        <form action="{{ route('simulation.reset') }}" method="POST">@csrf
            <button class="btn btn-danger rounded-3">Reset</button>
        </form>
    </div>

</div>
@endsection

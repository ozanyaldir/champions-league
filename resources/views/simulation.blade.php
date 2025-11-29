@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <h2 class="mb-4">Simulation</h2>

    {{-- League Table --}}
    <table class="table table-bordered mb-4">
        <thead class="bg-dark text-white">
            <tr>
                <th>Team Name</th>
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
                <td>{{ $row['team'] }}</td>
                <td>{{ $row['played'] }}</td>
                <td>{{ $row['won'] }}</td>
                <td>{{ $row['draw'] }}</td>
                <td>{{ $row['lost'] }}</td>
                <td>{{ $row['points'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Current Week Simulation --}}
    <h4>Week {{ $currentWeek ?? '-' }}</h4>
    <table class="table table-bordered mb-4">
        <thead class="bg-dark text-white">
            <tr>
                <th>Home</th>
                <th>Away</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($matches ?? [] as $m)
            <tr>
                <td>{{ $m['home'] }}</td>
                <td>{{ $m['away'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Predictions --}}
    <h4>Championship Predictions</h4>
    <table class="table table-bordered mb-4">
        <thead class="bg-dark text-white">
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

    <div class="d-flex gap-3">
        <form action="{{ route('simulation.playAll') }}" method="POST">@csrf
            <button class="btn btn-success">Play All</button>
        </form>

        <form action="{{ route('simulation.nextWeek') }}" method="POST">@csrf
            <button class="btn btn-primary">Play Next Week</button>
        </form>

        <form action="{{ route('simulation.reset') }}" method="POST">@csrf
            <button class="btn btn-danger">Reset</button>
        </form>
    </div>

</div>
@endsection

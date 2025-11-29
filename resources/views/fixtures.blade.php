@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- First Row: Teams and Predictions -->
    <div class="row mb-4">
        <!-- Teams Column -->
        <div class="col-md-6">
            <h2 class="mb-3">Attending Teams</h2>
            <ul class="list-group">
                @foreach($teams as $team)
                    <li class="list-group-item">{{ $team->name }}</li>
                @endforeach
            </ul>
        </div>

        <!-- Predictions Column -->
        <div class="col-md-6">
            <h2 class="mb-3">Predictions</h2>
            <ul class="list-group">
                @foreach($predictions ?? [] as $prediction)
                    <li class="list-group-item">{{ $prediction }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Second Row: Weeks -->
    <div class="row">
        @foreach ($weeks as $weekNumber => $matches)
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        Week {{ $weekNumber }}
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($matches as $match)
                            <li class="list-group-item">
                                {{ $match['home'] }} 
                                @if($match['away'] === 'Bye')
                                    has a bye
                                @else
                                    vs {{ $match['away'] }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Start Simulation Button -->
    <div class="mt-4">
        <form method="POST" action="{{ route('simulation.start') }}">
            @csrf
            <button class="btn btn-primary">Start Simulation</button>
        </form>
    </div>

</div>
@endsection

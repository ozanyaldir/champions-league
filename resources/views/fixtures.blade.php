@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Generated Fixtures</h2>

    {{-- Horizontal Layout --}}
    <div class="row">
        @foreach ($weeks as $weekNumber => $fixtures)
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        Week {{ $weekNumber }}
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($fixtures as $fixture)
                            <li class="list-group-item">
                                {{ $fixture->homeTeam?->name }} 
                                @if(!$fixture->awayTeam->name)
                                    ✕
                                @else
                                    - {{ $fixture->awayTeam->name }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Start Simulation --}}
    <div class="mt-4">
        <form method="POST" action="{{ route('simulation.start') }}">
            @csrf
            <button class="btn btn-primary">Start Simulation</button>
        </form>
    </div>

</div>
@endsection

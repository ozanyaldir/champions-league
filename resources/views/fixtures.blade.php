@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Generated Fixtures</h2>
    <div class="row">
        @foreach ($weeks as $weekNumber => $matches)
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm">
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

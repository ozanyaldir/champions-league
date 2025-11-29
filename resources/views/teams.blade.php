@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <h2 class="mb-4">Tournament Teams</h2>

    <div class="table-responsive mb-4">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Team Name</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teams ?? [] as $team)
                    <tr>
                        <td>{{ $team->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>No teams available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        <form action="{{ route('fixtures.generate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">
                Generate Fixtures
            </button>
        </form>
    </div>

</div>
@endsection

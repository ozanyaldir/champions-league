@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Tournament Teams</h2>

    <div style="max-width: 440px;">
        <div class="table-responsive rounded-3 shadow-sm">
            <table class="table mb-0">
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
                            <td class="text-center">No teams available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <form action="{{ route('fixtures.generate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary rounded-3">
                Generate Fixtures
            </button>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <h2 class="mb-4">Tournament Teams</h2>

    <table class="table table-bordered">
        <thead class="bg-dark text-white">
            <tr>
                <th>Team Name</th>
            </tr>
        </thead>
        <tbody>
            {{-- You will inject teams here --}}
            @foreach ($teams ?? [] as $team)
                <tr>
                    <td>{{ $team->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <form action="{{ route('fixtures.generate') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary">
            Generate Fixtures
        </button>
    </form>

</div>
@endsection

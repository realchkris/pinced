@extends('layouts.app')

@section('content')
    <h1>Results for "{{ $dish }}"</h1>

    @if (empty($results) || count($results) === 0)
        <p>No results found yet.</p>
    @else
        <ul>
            @foreach ($results as $result)
                <li>{{ $result['name'] }} – found at {{ $result['source_link'] }}</li>
            @endforeach
        </ul>
    @endif
@endsection
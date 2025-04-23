@extends('layouts.app')

@section('content')

	@include('partials.search-form')

	@if (empty($results) || count($results) === 0)
		<div class="container-secondary text-center mx-auto text-white p-2 text-sm text-pgray italic mt-2 mb-2 w-fit">No results found for "{{ $dish }}".</div>
	@else

		<div class="container-secondary text-white font-semibold p-2 mt-2 mb-2 w-fit">
			{{ count($results) }} result{{ count($results) > 1 ? 's' : '' }} for "{{ $dish }}"
		</div>

		<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 justify-items-center">
			@foreach ($results as $result)
				@include('partials.restaurant-card', ['restaurant' => $result])
			@endforeach
		</div>

	@endif

@endsection

{{--

@extends('layouts.app')

@section('content')

	@include('partials.search-form')

	<div class="container-secondary text-white font-semibold p-2 mt-2 mb-2 w-fit">X results for "{{ $dish }}"</div>

	<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 justify-items-center">
		@for ($i = 0; $i < 15; $i++)
			@include('partials.restaurant-card')
		@endfor
	</div>

@endsection

--}}
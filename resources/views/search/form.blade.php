@extends('layouts.app')

@section('content')

	@if ($errors->any())
		<div style="color:red;">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@include('partials.search-form')

@endsection
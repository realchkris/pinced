@extends('layouts.app')

@section('content')

	<h1>🔍 Search for a Dish</h1>

	@if ($errors->any())
		<div style="color:red;">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<form action="/" method="POST">
		@csrf
		<label for="dish">Dish name:</label><br>
		<input type="text" name="dish" id="dish" placeholder="e.g. carbonara" required><br><br>
		<button type="submit">Search</button>
	</form>

@endsection
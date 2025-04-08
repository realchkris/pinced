<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Pinced</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	@vite('resources/css/app.css')
</head>

<body class="bg-pegg font-sans min-h-screen">

	@include('partials.header')

	<main class="p-6 max-w-5xl mx-auto">
		@yield('content')
	</main>

</body>

</html>
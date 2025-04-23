<div class="container-primary text-white p-4 w-full flex flex-col gap-2">

	<!-- Image Placeholder -->
	<div class="container-tertiary rounded-lg h-32 w-full"></div>

	<!-- Restaurant Name -->
	<span class="container-quinary text-white font-bold px-3 py-1 inline-block w-fit">
		{{ $restaurant['name'] ?? 'Unknown Restaurant' }}
	</span>

	<!-- Location & Website -->
	<div class="flex flex-col gap-2 text-sm">
		<span class="container-quaternary text-white px-2 py-1 w-fit">
			{{ $restaurant['location'] ?? 'Unknown Location' }}
		</span>
		<a href="{{ $restaurant['source_link'] ?? '#' }}" target="_blank" class="container-quaternary text-white px-2 py-1 w-fit">
			Website
		</a>
	</div>

</div>

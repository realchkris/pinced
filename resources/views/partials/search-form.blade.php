<form
	action="{{ route('search.submit') }}"
	method="POST"
	class="flex flex-col gap-2"
	x-data="locationSearch()"
>
	@csrf

	<!-- Input forms -->
	<div class="flex gap-2">

		<!-- Dish -->
		<input
			class="input-primary w-1/2"
			type="text"
			name="dish"
			id="dish"
			placeholder="e.g. Carbonara"
			required
			maxlength="128"
		>

		<!-- Location -->
		<div class="relative w-1/2">

			<!-- Location Input field -->
			<input
				class="input-primary w-full"
				type="text"
				name="location"
				id="location"
				placeholder="e.g. Rome"
				autocomplete="off"

				x-model="query"
				@input.debounce.300="search"
				@focus="show = true"
				@click.away="handleBlur"
				required
			>

			<!-- Hidden input for country code -->
			<input type="hidden" name="country_code" x-model="countryCode">

			<!-- Hidden input for specific location -->
			<input type="hidden" name="location_specific" x-model="locationSpecific">

			<!-- Skeleton Dropdown -->
			<ul
				x-show="show && loading"
				class="skeleton-loader-primary mt-1 absolute z-10 w-full space-y-2 p-2"
			>
				<template x-for="i in 3">
					<li class="min-h-8 bg-pegg rounded w-full animate-pulse"></li>
				</template>
			</ul>

			<!-- Suggestions Dropdown -->
			<ul
				x-show="show && results.length && !loading"
				class="container-primary text-white mt-1 absolute z-10 w-full"
			>
				<template x-for="place in results" :key="place.place_id">
					<li
						@click="select(place)"
						class="px-3 py-2 hover:bg-pgreen-dark rounded-lg cursor-pointer text-sm"
						x-text="place.display_name"
					></li>
				</template>
			</ul>

		</div>

	</div>

	<!-- Submit button -->
	<button class="button-primary text-2xl self-center" type="submit">Search</button>

</form>

<script>

function locationSearch() {
	return {
		query: '', // User input bound to input field
		results: [], // Autocomplete results from Nominatim API
		show: false, // Show dropdown
		loading: false, // Loading state
		selected: null, // Selected place
		countryCode: '', // Country code
		locationSpecific: '', // Most specific place name

		search() {
			if (this.query.length < 2) {
				this.results = [];
				this.loading = false;
				return;
			}

			this.loading = true;

			fetch('/api/nominatim?q=' + encodeURIComponent(this.query))
				.then(res => res.json())
				.then(data => {
					this.results = data;
					this.loading = false;
				})
				.catch(() => {
					this.loading = false;
				});
		},

		select(place) {
			const address = place.address;

			this.query = place.display_name; // Full name shown to user
			this.locationSpecific = address.village || address.town || address.city || place.display_name; // Specific location
			this.countryCode = address.country_code.toUpperCase(); // Uppercase country code
			this.selected = place;
			this.show = false;
		},

		handleBlur() {
			if (!this.selected) {
				this.query = '';
				this.locationSpecific = '';
				this.countryCode = '';
			}
			this.show = false;
		}

	}
}

</script>

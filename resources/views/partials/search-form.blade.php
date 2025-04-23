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
		<input class="input-primary w-1/2" type="text" name="dish" id="dish" placeholder="e.g. Carbonara" required>

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
			query: '', // User input bound to input via x-model
			results: [], // Array of autocomplete results from Nominatim API
			show: false, // Whether the dropdown is visible or not
			loading: false, // Bool for loader
			selected: null, // Bool to check if user selected the location

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
						this.loading = false; // Hide loader when fetched
					})
					.catch(() => {
						this.loading = false; // Hide on error
					});

			},

			select(place) {

				this.query = place.display_name;
				this.selected = place; // Mark as selected
				this.show = false;

			},

			handleBlur() {
				// If no selection made, clear input
				if (!this.selected || this.query !== this.selected.display_name) {
					this.query = '';
				}
				this.show = false; // Hide suggestion dropdown
			}

		}
	}
</script>

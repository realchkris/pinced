<form action="{{ route('search.submit') }}" method="POST" class="flex flex-col gap-2">
    @csrf

    <!-- Input forms -->
    <div class="flex gap-2">
        <input class="input-primary w-1/2" type="text" name="dish" id="dish" placeholder="e.g. Carbonara" required>
        <input class="input-primary w-1/2" type="text" name="location" id="location" placeholder="e.g. Rome" required>
    </div>

    <!-- Submit button -->
    <button class="button-primary text-2xl self-center" type="submit">Search</button>

</form>
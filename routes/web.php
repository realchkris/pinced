<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Services\ResearchManager;

Route::get('/', [SearchController::class, 'index']);
Route::post('/', [SearchController::class, 'search'])->name('search.submit');

Route::get('/test-links', function (ResearchManager $researchManager) {

    // Define a test query (e.g., a dish and location)
    $query = "chicken restaurant new york"; // You can change this to any query you want to test

    // Test collectGoodLinks
    $goodLinks = $researchManager->collectGoodLinks($query);

    // Output the result
    return response()->json([
        'good_links' => $goodLinks,
    ]);
});
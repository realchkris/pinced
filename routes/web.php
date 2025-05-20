<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Services\ResearchOrchestrator;

Route::get('/', [SearchController::class, 'index']);
Route::post('/', [SearchController::class, 'search'])->name('search.submit');

Route::get('/test-scraper', function (ResearchOrchestrator $researchOrchestrator) {

	// Define a test query (e.g., a dish and location)
	//$query = "chicken restaurant new york"; // You can change this to any query you want to test

	// Test collectGoodLinks
	//$goodLinks = $researchOrchestrator->searchEngineScraper->collectGoodLinks('brave', $query);

	// Test prepopulated website list
	/*
	$websiteList = [
		'https://www.timeout.com/newyork/restaurants/the-best-fried-chicken-in-nyc',
		'https://www.coqodaq.com/',
		'https://chirpnyc.com/',
		'https://ny.eater.com/maps/best-roast-chicken-nyc',
		'https://www.louieschickennyc.com/',
		'https://www.thrillist.com/eat/new-york/best-nyc-restaurants-chicken',
		'https://www.charlespanfriedchicken.com/',
		'https://secretnyc.co/best-fried-chicken-in-nyc/',
		'https://guide.michelin.com/en/article/travel/best-fried-chicken-new-york-city',
	];
	*/

	$websiteList = [
		'https://gastroranking.it/ristoranti/lombardia/mantova/',
		'https://www.sucarbrusc.it/',
		'https://www.aigaribaldini.it/',
		'https://www.finedininglovers.it/mappe/migliori-ristoranti-mantova',
		'https://guide.michelin.com/it/it/lombardia/mantova/ristoranti',
		'https://ilcignoristorante.it/',
		'https://www.grifonebianco.com/',
		'https://www.ilrigolettoristorante.it/',
	];

	$results = $researchOrchestrator->pageScraper->batchScrape($websiteList);

	// Output the result
	dd($results); // Dump and Die to inspect

});
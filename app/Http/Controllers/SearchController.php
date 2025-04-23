<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ScraperService;

class SearchController extends Controller
{

	public function index()
	{
		return view('search.form');
	}

	public function search(Request $request)
	{
		$request->validate([
			'dish' => 'required|string|max:255',
			'location' => 'required|string|max:255',
		]);

		$dishName = $request->input('dish');
		$locationName = $request->input('location');

		// Future check DB/scraper code goes here
		$results = []; // Dummy results

		return view('search.results', [
			'dish' => $dishName,
			'location' => $locationName,
			'results' => $results,
		]);
	}

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
		]);

		$dishName = $request->input('dish');

		// Future check DB/scraper code goes here

		return view('search.results', [
			'dish' => $dishName,
			'results' => [] // placeholder
		]);
	}

}

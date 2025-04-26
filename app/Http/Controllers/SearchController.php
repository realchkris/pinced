<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QueryBuilder;
use App\Services\CountryLanguageResolver;
use App\Services\Translator;

class SearchController extends Controller
{
	protected CountryLanguageResolver $resolver;
	protected Translator $translator;

	public function __construct(
		CountryLanguageResolver $resolver,
		Translator $translator
	) {
		$this->resolver = $resolver;
		$this->translator = $translator;
	}

	public function index()
	{
		return view('search.form');
	}

	public function search(Request $request)
	{
		$request->validate([
			'dish' => 'required|string|max:255',
			'location' => 'required|string|max:255',
			'country_code' => 'required|string|size:2',
		]);

		$dishName = $request->input('dish');
		$locationName = $request->input('location');
		$countryCode = strtoupper($request->input('country_code'));

		$queryBuilder = new QueryBuilder(
			$dishName,
			'restaurant',
			$locationName,
			$countryCode,
			$this->resolver,
			$this->translator
		);

		$queries = $queryBuilder->build(); // Returns array of [specific, general] queries

		return view('search.results', [
			'dish' => $dishName,
			'location' => $locationName,
			'queries' => $queries,
		]);
	}
}

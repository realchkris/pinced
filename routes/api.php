<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

Route::get('/nominatim', function (Request $request) {
	try {
		$client = new Client();

		$res = $client->request('GET', config('services.nominatim.url'), [
			'query' => [
				'q' => $request->query('q'),
				'format' => 'json',
				'addressdetails' => 1,
				'limit' => 5,
			],
			'headers' => [
				'User-Agent' => config('services.nominatim.user_agent'),
			],
		]);

		return response($res->getBody())->header('Content-Type', 'application/json');
	} catch (\Throwable $e) {
		Log::error('Nominatim API error: ' . $e->getMessage());
		return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
	}
});
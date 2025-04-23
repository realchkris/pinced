<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ScraperService
{
	public function searchDish(array $restaurants, string $dish, string $location): array
	{

		$results = [];

		foreach ($restaurants as $restaurant) {
			if (strtolower($restaurant['location']) !== strtolower($location)) {
				continue;
			}

			try {
				$response = Http::get($restaurant['url']);

				if ($response->ok()) {
					$html = $response->body();
					$crawler = new Crawler($html);
					$text = strtolower($crawler->filter('body')->text());

					if (str_contains($text, strtolower($dish))) {
						$results[] = [
							'name' => $restaurant['name'],
							'location' => $restaurant['location'],
							'source_link' => $restaurant['url'],
						];
					}
				}
			} catch (\Exception $e) {
				// Optional: log error
				continue;
			}
		}

		return $results;
	}
	
}

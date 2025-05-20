<?php

/**
 * Scrapes a single page (restaurant or aggregator) to detect restaurant data.
 *
 * Coordinates fetchers and detectors to extract structured restaurant information.
 *
 * Responsibilities:
 * - Fetch a single page's HTML
 * - Delegate detection to RestaurantDetectorManager
 * - Return structured restaurant data
 *
 * It does NOT:
 * - Detect search result links
 * - Scrape multiple URLs (this is done upstream)
 */

namespace App\Services\Scraper;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;

use App\Services\DTO\RestaurantDTO;
use App\Services\Detector\RestaurantDetectorManager;
use App\Services\Deduplicator\RestaurantDeduplicator;

class PageScraper extends Scraper
{

	protected $restaurantDetectorManager;

	/**
	 * Initializes the page scraper and its restaurant detector.
	 */
	public function __construct()
	{

		// Call parent constructor
		parent::__construct();

		$this->restaurantDetectorManager = new RestaurantDetectorManager();
	}

	/**
	 * Scrapes multiple URLs sequentially and returns detected restaurant data, plus per-URL errors.
	 *
	 * Fetches each page, applies detection, and handles failures gracefully.
	 *
	 * @param string[] $urls Array of page URLs to scrape
	 * @return array Associative array of results keyed by URL
	 */
	public function batchScrape(array $urls): array
	{
		$restaurants = [];
		$errors = [];

		// Looping through all the URLs
		foreach ($urls as $url) {
			try {

				// Getting the contents
				$response = $this->fetchWithRetry($url);
				$html = $response->getBody()->getContents();

				// Scraping each page
				$result = $this->scrapePage($html, $url);

				if (isset($result['restaurants']) && is_array($result['restaurants'])) {
					// Deduplicating all restaurants found in search
					RestaurantDeduplicator::merge($result['restaurants']);
				}

			} catch (\Exception $e) {
				$errors[$url] = $e->getMessage();
			}
		}

		return [
			'restaurants' => $restaurants,
			'errors' => $errors,
		];
	}

	/**
	 * Scrapes a single page's HTML content to extract restaurant data.
	 *
	 * @param string $html The raw HTML content of the page
	 * @param string|null $url Optional source URL, used for logging or error tracking
	 * @return array An array of detected restaurant entries
	 */
	protected function scrapePage(string $html, string $url = null): array
	{
		$crawler = new Crawler($html);
		$rawRestaurants = $this->restaurantDetectorManager->detect($crawler);

		$structured = [];

		foreach ($rawRestaurants as $r) {
			$structured[] = new RestaurantDTO(
				$r['name'] ?? '',
				$r['address'] ?? '',
				$url
			);
		}

		// Deduplicating restaurants found in page
		RestaurantDeduplicator::merge($rawRestaurants);

		return [
			'restaurants' => $structured,
		];
	}

}

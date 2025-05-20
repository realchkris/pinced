<?php

/**
 * Abstract base class for all scrapers.
 *
 * Provides shared functionality for fetching HTML, handling retries, or shared scraper logic.
 * Concrete scrapers (Page, SearchEngine) extend this to specialize behavior.
 *
 * Responsibilities:
 * - Provide a base for common scraping methods
 * - Define interface contracts for concrete scrapers
 *
 * It does NOT:
 * - Contain any DOM logic or parsing responsibilities
 */

namespace App\Services\Scraper;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;

abstract class Scraper
{	

	protected array $proxies = [];
    protected array $userAgents = [];
	protected Client $client;

	/*
		Constructor (loads proxies and userAgents).
	*/
	public function __construct()
	{
		$this->proxies = $this->loadProxiesFromFile();
		$this->userAgents = config('scraper.user_agents');
	}

	/*
		Loads a bunch of proxies from the proxy file (proxies_list.txt).
	*/
	protected function loadProxiesFromFile(): array
	{
		if (!Storage::exists('proxies/proxies_list.txt')) {
			return [];
		}

		$content = Storage::get('proxies/proxies_list.txt');
		$lines = array_filter(array_map('trim', explode(PHP_EOL, $content)));

		return $lines;
	}

	/*
		Returns a random proxy.
	*/
	protected function getRandomProxy(): ?string
	{
		if (empty($this->proxies)) {
			return null;
		}

		return $this->proxies[array_rand($this->proxies)];
	}

	/*
		Create client with random proxy and user agent.
	*/
	protected function createHttpClient(): Client
	{
		$randomProxy = $this->getRandomProxy(); // Can return null
		$randomUserAgent = $this->userAgents[array_rand($this->userAgents)] ?? 'Mozilla/5.0 (compatible; YourBot/1.0)';

		$options = [
			'timeout' => 10,
			'verify' => false,
			'headers' => [
				'User-Agent' => $randomUserAgent,
			],
		];

		if (!empty($randomProxy)) {
			$options['proxy'] = 'http://' . $randomProxy;
		}

		return new Client($options);
	}

	/*
		Fetch with retry with a bunch of different proxies.
	*/
	protected function fetchWithRetry(string $url, int $maxAttempts = 5)
	{
		$attempt = 0;

		do {
			try {
				$client = $this->createHttpClient();
				$response = $client->get($url);

				// If 202 response, treat as a failure and retry
				if ($response->getStatusCode() !== 200) {
					throw new \Exception("Invalid HTTP status: " . $response->getStatusCode());
				}

				return $response; // Success!

			} catch (\Exception $e) {
				$attempt++;

				if ($attempt >= $maxAttempts) {
					throw new \Exception("Failed after {$maxAttempts} attempts: " . $e->getMessage());
				}

				usleep(300000); // 0.3 sec pause
				continue;
			}
		} while ($attempt < $maxAttempts);
	}

}

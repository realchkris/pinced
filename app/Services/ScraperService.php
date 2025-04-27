<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use Symfony\Component\DomCrawler\Crawler;

class ScraperService
{
	protected Client $client;

	public function __construct()
	{
		$this->client = new Client([
			'timeout' => 10,
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (compatible; PincedBot/1.0; +https://yourdomain.com/bot)'
			],
		]);
	}

	public function searchBingUrl(string $url): string
	{
		$response = $this->client->get($url);

		if ($response->getStatusCode() !== 200) {
			throw new \Exception("Failed to fetch Bing search results");
		}

		return $response->getBody()->getContents();
	}

	public function extractLinksFromBing(string $html): array
	{
		$crawler = new Crawler($html);

		$links = [];

		// Bing search results are contained in <li class="b_algo"> elements
		$crawler->filter('li.b_algo h2 a')->each(function (Crawler $node) use (&$links) {
			$url = $node->attr('href');
			
			// Only include URLs that are valid and not part of the sponsored/ads
			if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
				$links[] = $url;
			}
		});

		return $links;
	}

}

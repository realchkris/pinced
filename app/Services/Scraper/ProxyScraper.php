<?php

/**
 * Scraper that supports proxy rotation for bypassing rate limits or geo-restrictions.
 *
 * Responsibilities:
 * - Rotate proxies during fetch
 * - Handle blocked responses or captchas
 *
 * It does NOT:
 * - Implement parsing or detection logic
 */

namespace App\Services\Scraper;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;

class ProxyScraper
{
	protected Client $client;
	protected array $proxySources = [];
	protected array $headerMatch = [
		'ip' => ['ip', 'address', 'proxy'],
		'port' => ['port'],
		'anonymity' => ['anon', 'anonymity', 'level'],
		'https' => ['https', 'ssl', 'type'],
	];	

	public function __construct()
	{
		$this->client = new Client([
			'timeout' => 10,
			'verify' => false,
		]);

		$this->proxySources = config('scraper.proxy_sources');
	}

	protected function detectColumnIndexes(Crawler $headerRow): array
	{
		$columnMap = [
			'ip' => null,
			'port' => null,
			'anonymity' => null,
			'https' => null,
		];

		$headerRow->each(function (Crawler $thNode, $index) use (&$columnMap) {
			$text = strtolower(trim($thNode->text()));

			foreach ($this->headerMatch as $type => $keywords) {
				foreach ($keywords as $keyword) {
					if (str_contains($text, $keyword)) {
						$columnMap[$type] = $index;
						break 2; // Once matched, stop checking
					}
				}
			}
		});

		return $columnMap;
	}

	public function scrapeProxies(): array
	{
		$proxies = [];

		foreach ($this->proxySources as $url) {
			try {
				$response = $this->client->get($url);

				if ($response->getStatusCode() !== 200) {
					continue;
				}

				$body = $response->getBody()->getContents();

				$proxies = array_merge($proxies, $this->parseHtmlProxies($body));

			} catch (\Exception $e) {
				// Log error if needed
				continue;
			}
		}

		return array_unique($proxies);
	}

	protected function parseHtmlProxies(string $body): array
	{
		$proxies = [];

		$crawler = new Crawler($body);

		$headerRow = $crawler->filter('table thead tr th');
		$columnMap = $this->detectColumnIndexes($headerRow);

		if (in_array(null, $columnMap, true)) {
			return []; // Missing important columns
		}

		$crawler->filter('table tbody tr')->each(function (Crawler $row) use (&$proxies, $columnMap) {
			$columns = $row->filter('td');

			if ($columns->count() > max($columnMap)) {
				$ip = trim($columns->eq($columnMap['ip'])->text());
				$port = trim($columns->eq($columnMap['port'])->text());
				$anonymity = strtolower(trim($columns->eq($columnMap['anonymity'])->text()));
				$https = strtolower(trim($columns->eq($columnMap['https'])->text()));

				$isHttpsSupported = str_contains($https, 'yes') || str_contains($https, 'https') || str_contains($https, 'ssl');
				$isEliteOrHigh = str_contains($anonymity, 'elite') || str_contains($anonymity, 'high') || str_contains($anonymity, 'hia');

				if ($isHttpsSupported && $isEliteOrHigh) {
					$proxies[] = "{$ip}:{$port}";
				}
			}
		});

		return $proxies;
	}

	public function saveProxiesToFile(array $proxies): void
	{
		$content = implode(PHP_EOL, $proxies);
		Storage::disk('local')->put('proxies/proxies_list.txt', $content);
	}

}

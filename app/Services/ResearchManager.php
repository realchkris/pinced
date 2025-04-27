<?php

namespace App\Services;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Http;

use App\Services\ScraperService;

class ResearchManager
{
	protected ScraperService $scraper;

	protected int $maxLinks = 15; // Total links allowed to scrape per query

	protected int $optimisticThreshold = 3; // Number of parallel batches (optimistic UI)
	protected int $fullThreshold = 5; // Minimum required before switching to general query

	public function __construct(ScraperService $scraper)
	{
		$this->scraper = $scraper;
	}

	public function research(array $queries): array
	{
		$allResults = [];

		// 1. SPECIFIC Query First
		$specificQuery = $queries['specific'] ?? null;
		if ($specificQuery) {
			$allResults = $this->handleQuery($specificQuery, $allResults);

			if (count($allResults) < $this->fullThreshold) {
				// 2. If not enough → GENERAL Query
				$generalQuery = $queries['general'] ?? null;
				if ($generalQuery) {
					$allResults = $this->handleQuery($generalQuery, $allResults);
				}
			}
		}

		return [
			'full' => array_values($allResults),
		];
	}

	protected function handleQuery(string $query, array $existingResults): array
	{
		// Step 1: Collect good links from Bing search (pagination & filtering)
		$goodLinks = $this->collectGoodLinks($query);

		// Step 2: Scrape restaurant websites in parallel (batch)
		$batchResults = $this->scraper->batchScrape($goodLinks);

		// Step 3: Merge new results into existing ones
		foreach ($batchResults as $result) {
			$existingResults[] = $result;
		}

		// Step 4: Push optimistic update (display progress in UI)
		$this->pushOptimisticResults($existingResults);

		return $existingResults;
	}

	public function buildBingUrl(string $query, int $offset = 1): string
	{
		$encodedQuery = urlencode($query);
		return "https://www.bing.com/search?q={$encodedQuery}&first={$offset}";
	}

	public function collectGoodLinks(string $query): array
	{
		$goodLinks = []; // Array that stores the links
		$goodDomains = []; // Array that stores the domains (used to avoid duplicate domains)
		$blockedDomains = config('scraper.blocked_domains');

		$page = 0;

		// Using maxLinks to stop once there are enough links
		while (count($goodLinks) < $this->maxLinks) {
			$offset = $page * 10 + 1; // Bing paginates by 10 results per page

			dump("Bing page offset:{$offset}");

			// 1. Build Bing URL for this page
			$bingUrl = $this->buildBingUrl($query, $offset);

			// 2. Fetch Bing page
			$bingHtml = $this->scraper->searchBingUrl($bingUrl);

			// 3. Extract links from Bing HTML
			$links = $this->scraper->extractLinksFromBing($bingHtml);

			dump("Links extracted.");

			if (empty($links)) {
				// No more results available
				break;
			}

			$newLinksFound = false;

			dump("Filtering links...");

			// 4. Filter links
			foreach ($links as $link) {
				$domain = parse_url($link, PHP_URL_HOST);
				$domain = str_replace('www.', '', $domain); // Normalize domains

				if (empty($domain)) {
					continue; // Skip malformed links
				}

				if (in_array($domain, $blockedDomains)) {
					continue; // Skip known bad domains
				}

				if (in_array($domain, $goodDomains)) {
					continue; // Skip duplicate domains
				}

				// 5. Add good, unique link
				$goodLinks[] = $link;
				$goodDomains[] = $domain;
				$newLinksFound = true;

				// 6. If enough good links, break
				if (count($goodLinks) >= $this->maxLinks) {
					dump("Enough good links. Break.");
					break 2; // Break both foreach and while loops
				}
			}

			if (!$newLinksFound) {
				dump("No new good links found. Break.");
				// If after parsing page no new good links were added, probably no point continuing
				break;
			}

			dump("Moving to next page...");

			$page++; // Move to next Bing page
		}

		return $goodLinks;
	}

	protected function pushOptimisticResults(array $results): void
	{
		// Here you could:
		// - Broadcast an event
		// - Cache temporary results
		// - Push via Livewire or other mechanism

		// Example: broadcasting an event
		// event(new OptimisticResultsUpdated($results));
	}

}

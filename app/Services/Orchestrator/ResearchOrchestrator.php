<?php

/**
 * Coordinates the full research process for a given set of queries.
 *
 * Executes search engine lookups, fetches restaurant page data, and applies fallback strategies
 * if the initial query does not yield enough results.
 *
 * Responsibilities:
 * - Run specific and general queries in order
 * - Manage flow from query → search results → restaurant data
 * - Push updates to the UI when partial results are available
 *
 * It does NOT:
 * - Parse or detect restaurant data from HTML (delegates to PageScraper)
 * - Scrape search engines directly (delegates to SearchEngineScraper)
 */

namespace App\Services;
use Illuminate\Support\Facades\Http;

use App\Services\Scraper\PageScraper;
use App\Services\Scraper\SearchEngineScraper;

class ResearchOrchestrator
{
	public PageScraper $pageScraper;
	public SearchEngineScraper $searchEngineScraper;

	protected int $maxLinks = 15; // Max total results

	protected int $optimisticThreshold = 3; // When to push UI updates
	protected int $fullThreshold = 5; // Min results before falling back

	/**
	 * Initializes the orchestrator with scrapers for pages and search engine results.
	 *
	 * @param PageScraper $pageScraper Scraper for restaurant web pages
	 * @param SearchEngineScraper $searchEngineScraper Scraper for search result pages
	 */
	public function __construct(PageScraper $pageScraper, SearchEngineScraper $searchEngineScraper)
	{
		$this->pageScraper = $pageScraper;
		$this->searchEngineScraper = $searchEngineScraper;
	}

	/**
	 * Runs a research cycle using specific and general queries.
	 *
	 * If the specific query yields too few results, falls back to a general query.
	 *
	 * @param array $queries An associative array with 'specific' and 'general' keys
	 * @return array An array of finalized results under the 'full' key
	 */
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

	/**
	 * Handles a single query: collects links, scrapes pages, and merges results.
	 *
	 * @param string $query The search query to process
	 * @param array $existingResults Results from previous queries to merge with
	 * @return array Updated list of results after processing this query
	 */
	protected function handleQuery(string $query, array $existingResults): array
	{
		// Step 1: Given a research query, collect search engine links from Search engine search.
		$goodLinks = $this->searchEngineScraper->collectGoodLinks($query);

		// Step 2: Scrape restaurant websites in parallel (batch)
		$batchResults = $this->pageScraper->batchScrape($goodLinks);

		// Step 3: Merge new results into existing ones
		foreach ($batchResults as $result) {
			$existingResults[] = $result;
		}

		// Step 4: Push optimistic update (display progress in UI)
		$this->pushOptimisticResults($existingResults);

		return $existingResults;
	}

	/**
	 * Pushes results to the UI after a batch completes.
	 */
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

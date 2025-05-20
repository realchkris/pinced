<?php

/**
 * Scrapes search engine result pages for external links.
 *
 * Handles building the query URL, fetching the result page, and extracting valid links.
 *
 * Responsibilities:
 * - Perform searches via supported engines
 * - Extract valid result links using LinkDetector
 * - Filter and deduplicate the link list
 *
 * It does NOT:
 * - Scrape content from the result links (that’s PageScraper's job)
 */

namespace App\Services\Scraper;

use Symfony\Component\DomCrawler\Crawler;
use App\Services\Detector\LinkDetector;

class SearchEngineScraper extends Scraper
{

	protected LinkDetector $linkDetector;

	public function __construct(LinkDetector $linkDetector)
	{
		$this->linkDetector = $linkDetector;
	}

	/*
		Public main function: orchestrates research and filtering.
	*/
	public function collectGoodLinks(string $engine, string $query, int $maxLinks = 15): array
	{
		$rawLinks = $this->searchAndExtractLinks($engine, $query);
		return $this->filterGoodLinks($rawLinks, $maxLinks);
	}

	/*
		Fetches the search engine page and extracts all links.
	*/
	protected function searchAndExtractLinks(string $engine, string $query): array
	{
		// 1. Build URL
		$searchEngineUrl = $this->buildSearchUrl($engine, $query);

		// 2. Fetch page
		$searchEngineHtml = $this->fetchSearchPage($searchEngineUrl);

		// (Optional) Save debug copy
		file_put_contents(storage_path("search_engine_debug_{$engine}.html"), $searchEngineHtml);

		// 3. Extract links
		return $this->linkDetector->extractLinks($engine, $html);
	}

	/*
		Filters links: removes blocked domains, duplicates, malformed links.
	*/
	protected function filterGoodLinks(array $links, int $maxLinks = 15): array
	{
		$goodLinks = [];
		$goodDomains = [];
		$blockedDomains = config('scraper.blocked_domains');

		foreach ($links as $link) {
			$domain = parse_url($link, PHP_URL_HOST);
			$domain = str_replace('www.', '', $domain);

			if (empty($domain)) {
				continue;
			}

			if (in_array($domain, $blockedDomains)) {
				continue;
			}

			if (in_array($domain, $goodDomains)) {
				continue;
			}

			$goodLinks[] = $link;
			$goodDomains[] = $domain;

			if (count($goodLinks) >= $maxLinks) {
				break;
			}
		}

		return $goodLinks;
	}

	/*
		Fetch a search engine page by URL.
	*/
	public function fetchSearchPage(string $url): string
	{

		dump($url);
		$response = $this->fetchWithRetry($url);

		if ($response->getStatusCode() !== 200) {
			throw new \Exception("Failed to fetch search engine page. HTTP " . $response->getStatusCode());
		}

		return $response->getBody()->getContents();
	}

	/*
		Builds a search URL given engine and query.
	*/
	public function buildSearchUrl(string $engine, string $query): string
	{
		if (!isset($this->engines[$engine])) {
			throw new \Exception("Unsupported search engine: {$engine}");
		}

		$config = $this->engines[$engine];
		$url = str_replace('{query}', urlencode($query), $config['base_url']);

		return $url;
	}

	/*
		Prevents DDG link redirection
	*/
	private function extractDuckDuckGoRedirect(string $href): ?string
	{
		$parsed = parse_url($href);

		if (isset($parsed['query'])) {
			parse_str($parsed['query'], $queryParts);

			if (isset($queryParts['uddg'])) {
				$realUrl = urldecode($queryParts['uddg']);

				if (filter_var($realUrl, FILTER_VALIDATE_URL)) {
					return $realUrl;
				}
			}
		}

		return null;
	}

	/*
		Returns the supported engines.
	*/
	public function supportedEngines(): array
	{
		return $this->linkDetector->supportedEngines();
	}

}

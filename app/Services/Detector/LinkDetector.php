<?php

namespace App\Services\Detector;

use Symfony\Component\DomCrawler\Crawler;

class LinkDetector extends Detector
{

	// Engines and respective selectors
	protected array $engines = [
		'duckduckgo' => [
			'base_url' => 'https://html.duckduckgo.com/html/?q={query}',
			'selector' => '.result__title a',
		],
		'brave' => [
			'base_url' => 'https://search.brave.com/search?q={query}',
			'selector' => 'a.heading-serpresult',
		],
		/*
		'startpage' => [
			'base_url' => 'https://www.startpage.com/sp/search?query={query}',
			'selector' => '.w-gl__result-title a',
		],
		'qwant' => [
			'base_url' => 'https://www.qwant.com/?q={query}&t=web',
			'selector' => '.result--url a',
		],
		*/
	];

	public function extractLinks(string $engine, string $html): array
	{
		if (!isset($this->engines[$engine])) {
			throw new \Exception("Unsupported search engine: {$engine}");
		}

		$crawler = new Crawler($html);
		$selector = $this->engines[$engine];
		$links = [];

		$crawler->filter($selector)->each(function ($node) use (&$links, $engine) {
			$href = $node->attr('href');
			if (!$href) return;

			if ($engine === 'duckduckgo' && str_starts_with($href, '/l/')) {
				$realUrl = $this->extractDuckDuckGoRedirect($href);
				if ($realUrl) $links[] = $realUrl;
			} elseif (filter_var($href, FILTER_VALIDATE_URL)) {
				$links[] = $href;
			}
		});

		return $links;
	}

	protected function extractDuckDuckGoRedirect(string $href): ?string
	{
		$parsed = parse_url($href);
		if (isset($parsed['query'])) {
			parse_str($parsed['query'], $queryParts);
			if (isset($queryParts['uddg'])) {
				$url = urldecode($queryParts['uddg']);
				return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
			}
		}
		return null;
	}

	public function supportedEngines(): array
	{
		return array_keys($this->engines);
	}
}

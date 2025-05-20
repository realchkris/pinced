<?php

/**
 * Abstract class.
 * Understand the DOM — find links, names, addresses
 */

namespace App\Services\Detector;

use Symfony\Component\DomCrawler\Crawler;
use App\Services\Detector\Detector;

abstract class Detector
{
	/**
	 * Basic pattern search utility.
	 */
	protected function patternSearch(Crawler $crawler, array $selectors, callable $filter = null): array
	{
		$results = [];

		foreach ($selectors as $selector) {
			$crawler->filter($selector)->each(function (Crawler $node) use (&$results, $filter) {
				$text = trim($node->text());

				if ($filter) {
					$result = $filter($text);
					if ($result !== null) {
						$results[] = $result;
					}
				} else {
					$results[] = $text;
				}
			});
		}

		return $results;
	}
}

<?php

/**
 * Detects restaurant names and addresses from a single page DOM.
 *
 * Acts as a coordinator for guessers and extractors. Handles pairing and validation.
 *
 * Responsibilities:
 * - Identify candidate name/address blocks
 * - Pair names with matching addresses
 * - Return structured restaurant data
 *
 * It does NOT:
 * - Fetch pages
 * - Apply fallback logic (this should be outside its scope)
 */

namespace App\Services\Detector;

use Symfony\Component\DomCrawler\Crawler;
use App\Services\Detector\Detector;

use App\Services\DTO\RestaurantDTO;
use App\Services\Deduplicator\RestaurantDeduplicator;

class HeuristicDetector extends Detector
{

	/**
	 * Detects restaurants inside a page.
	 */
	public function detect(Crawler $crawler): array
	{



	}

}
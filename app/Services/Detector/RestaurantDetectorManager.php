<?php

namespace App\Services\Detector;

use App\Services\DTO\RestaurantDTO;
use App\Services\Detector\JsonLdDetector;
use App\Services\Detector\MicrodataDetector;
use App\Services\Detector\HeuristicDetector;

class RestaurantDetectorManager
{
	protected array $detectors;

	public function __construct()
	{
		$this->detectors = [
			new JsonLdDetector(),
			new MicrodataDetector(),
			new HeuristicDetector(), // fallback
		];
	}

	/**
	 * Tries each detector in order and returns the first non-empty result.
	 *
	 * @param string $html Raw HTML
	 * @param string|null $url Optional source URL
	 * @return RestaurantDTO[]
	 */
	public function detect(string $html, ?string $url = null): array
	{
		foreach ($this->detectors as $detector) {
			$results = $detector->detect($html, $url);

			if (!empty($results)) {
				return $results;
			}
		}

		return [];
	}
}

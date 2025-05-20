<?php

namespace App\Services\Deduplicator;

use App\Services\DTO\RestaurantDTO;

class RestaurantDeduplicator
{
    public static function isDuplicate(array $existing, RestaurantDTO $candidate): bool
    {
        foreach ($existing as $r) {
            if (
                self::normalize($r->name) === self::normalize($candidate->name) &&
                self::normalize($r->address) === self::normalize($candidate->address)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
	 * Merges fields where name or address matches
	 */
	public static function merge(array $restaurants): array
	{
		$merged = [];

		foreach ($restaurants as $current) {
			$found = false;

			foreach ($merged as &$existing) {
				// Normalize both sides: lowercase and trimmed
				$currentName = strtolower(trim($current['name'] ?? ''));
				$currentAddress = strtolower(trim($current['address'] ?? ''));
				$existingName = strtolower(trim($existing['name'] ?? ''));
				$existingAddress = strtolower(trim($existing['address'] ?? ''));

				// Matching by normalized name or normalized address
				if (
					(!empty($currentName) && $currentName === $existingName) ||
					(!empty($currentAddress) && $currentAddress === $existingAddress)
				) {
					// Merge missing fields
					if (empty($existing['name']) && !empty($current['name'])) {
						$existing['name'] = $current['name'];
					}

					if (empty($existing['address']) && !empty($current['address'])) {
						$existing['address'] = $current['address'];
					}

					$found = true;
					break;
				}
			}

			if (!$found) {
				$merged[] = $current;
			}
		}

		return $merged;
	}


    protected static function normalize(string $text): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $text)));
    }

}

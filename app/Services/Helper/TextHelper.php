<?php

namespace App\Services\Helper;

class TextHelper {

    public array $noiseKeywords; // Keywords for page elements to avoid

    public function __construct(){
        $this->noiseKeywords = config('scraper.noise_keywords');
    }

    /**
	 * Check if a text contains any noise keywords.
	 */
	public static function containsNoiseWords(string $text): bool
	{
		$text = strtolower($text); // Normalize to lowercase

		foreach ($this->noiseKeywords as $noiseWord) {
			if (strpos($text, $noiseWord) !== false) {
				return true;
			}
		}

		return false;
	}

    /**
	 * Check if a text has too many repeating words.
	 */
	public static function hasTooManyRepeatingWords(string $text): bool
	{
		$words = array_filter(explode(' ', strtolower($text))); // Normalize lowercase

		$counts = array_count_values($words);

		foreach ($counts as $word => $count) {
			if ($count > 2 && strlen($word) > 2) { 
				// More than 2 repetitions of a non-tiny word → suspicious
				return true;
			}
		}

		return false;
	}

	/**
	 * Clears text.
	 */
	public static function clean(string $text): string
	{
		// Convert non-breaking spaces to normal spaces
		$text = str_replace(["\u{A0}", "\u{00A0}", "\xC2\xA0"], ' ', $text);

		// Remove any other invisible control characters
		$text = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $text);

		// Collapse multiple spaces into one
		$text = preg_replace('/\s+/', ' ', $text);

		// Trim spaces
		return trim($text);
	}

}
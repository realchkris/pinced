<?php

/**
 * Provides utility functions to detect and validate address-like strings.
 *
 * Includes regex for street names, postal codes, and common patterns.
 *
 * Responsibilities:
 * - Identify if a string looks like an address
 * - Score or rank address candidates
 *
 * It does NOT:
 * - Extract addresses from DOM (that's for detectors)
 */

class AddressHelper {

    protected array $addressKeywords; // Keywords to match addresses

    public function __construct(){
        $this->addressKeywords = config('scraper.address_keywords');
    }

    /**
	 * Heuristic to check if text looks like an address.
	 */
	public static function isValid(string $text): bool
	{
		$text = trim($text);

		// Max length check first (defensive programming)
		if (strlen($text) > 200) {
			return false; // Way too long to be a single address
		}

		// Repeating word check
		if ($this->hasTooManyRepeatingWords($text)) {
			return false;
		}

		// Basic street number and word pattern
		if (preg_match('/\d{1,5}\s\w+/', $text)) {
			return true;
		}

		// Check if any address-like keyword appears
		foreach ($this->addressKeywords as $keyword) {
			if (stripos($text, $keyword) !== false) {
				return true;
			}
		}

		// City + postal code pattern (e.g., "75001 Paris", "1010 Vienna")
		if (preg_match('/\d{4,5}\s[A-Z][a-z]+/', $text)) {
			return true;
		}

		// Comma-separated address style (e.g., "123 Main St, New York")
		if (substr_count($text, ',') >= 1 && str_word_count($text) > 3) {
			return true;
		}

		return false;
	}

}
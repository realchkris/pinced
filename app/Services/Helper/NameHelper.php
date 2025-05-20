<?php

class NameHelper {

    /**
	 * Heuristic to check if a text looks like a valid restaurant name.
	 */
	public static function isValidName(string $text): bool
	{
		$text = trim($text);

		// 1. Too short: skip
		if (strlen($text) < 3) return false;

		// 2. Too many words: skip
		if (str_word_count($text) > 6) return false;

		// 3. Suspicious ending punctuation: skip
		if (substr($text, -1) === '.' || substr($text, -1) === '!' || substr($text, -1) === '?') return false;

		// 4. Too many commas: skip
		if (substr_count($text, ',') > 2) return false;

		// 5. Not enough capitalization: skip
		if (!$this->hasEnoughCapitalizedWords($text)) return false;

		// 6. Insanely long strings: skip
		if (strlen($text) > 100) return false;

		// Otherwise, valid
		return true;
	}

    /**
	 * Checks if a text has enough capitalized words.
	 */
	public static function hasEnoughCapitalizedWords(string $text): bool
	{
		$words = explode(' ', $text);
		$capitalized = 0;

		foreach ($words as $word) {
			if (strlen($word) < 2) continue;
			if (ctype_upper(mb_substr($word, 0, 1))) $capitalized++;
		}

		return $capitalized >= (count($words) / 2);
	}

}
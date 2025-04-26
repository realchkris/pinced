<?php

namespace App\Services;

class CountryLanguageResolver
{
    protected static array $countries = [];

    /**
     * Load countries data from the composer package (only once).
     */
    protected static function loadCountries(): void
    {
        if (empty(self::$countries)) {
            $json = file_get_contents(base_path('vendor/annexare/countries-list/dist/countries.min.json'));
            self::$countries = json_decode($json, true);
        }
    }

    /**
     * Resolve the primary language for a given country code.
     */
    public static function resolve(string $countryCode): ?string
    {
        self::loadCountries();

        $countryCode = strtoupper($countryCode);

        if (!isset(self::$countries[$countryCode])) {
            return 'en'; // fallback to English if not found
        }

        $languages = self::$countries[$countryCode]['languages'] ?? [];

        return $languages[0] ?? 'en'; // fallback to English if empty
    }

    /**
     * Resolve all languages for a given country.
     */
    public static function resolveAll(string $countryCode): array
    {
        self::loadCountries();

        $countryCode = strtoupper($countryCode);

        if (!isset(self::$countries[$countryCode])) {
            return ['en']; // fallback
        }

        return self::$countries[$countryCode]['languages'] ?? ['en'];
    }
}

<?php

namespace App\Services;

class QueryBuilder
{
	public function __construct(
		protected string $dish,
		protected string $restaurant,
		protected string $location,
		protected string $countryCode,
		protected CountryLanguageResolver $resolver,
		protected Translator $translator,
	) {}

	public function build(): array
	{
		$countryLang = $this->resolver->resolve($this->countryCode);

		$finalDish = $this->dish;
		$finalRestaurant = $this->restaurant;

		if ($countryLang !== 'en') {
			$finalDish = $this->translator->translate($this->dish, $countryLang);
			$finalRestaurant = $this->translator->translate($this->restaurant, $countryLang);
		}

		$specificQuery = $this->buildSpecificQuery($finalDish, $finalRestaurant);
		$generalQuery = $this->buildGeneralQuery($finalRestaurant);

		return [
			[
				'lang' => $countryLang,
				'query' => $specificQuery,
				'type' => 'specific',
			],
			[
				'lang' => $countryLang,
				'query' => $generalQuery,
				'type' => 'general',
			],
		];
	}

	private function buildSpecificQuery(string $dish, string $restaurant): string
	{
		return "{$dish} {$restaurant} {$this->location}";
	}

	private function buildGeneralQuery(string $restaurant): string
	{
		return "{$restaurant} {$this->location}";
	}
}

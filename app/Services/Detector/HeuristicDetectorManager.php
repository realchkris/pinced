<?php

namespace App\Services\Detector;

use Symfony\Component\DomCrawler\Crawler;
use App\Services\DTO\RestaurantDTO;

class HeuristicDetectorManager
{
    protected HeuristicDetector $detector;

    public function __construct()
    {
        $this->detector = new HeuristicDetector();
    }

    /**
     * Main detection entrypoint.
     *
     * Determines page type and delegates to the appropriate heuristic strategy.
     *
     * @param Crawler $crawler
     * @return RestaurantDTO[]
     */
    public function detect(Crawler $crawler): array
    {
        if ($this->isAggregator($crawler)) {
            return $this->detectFromMultipleBlocks($crawler);
        }

        return $this->detectFromSinglePage($crawler);
    }

    /**
     * Tries to determine if the page is an aggregator.
     *
     * @param Crawler $crawler
     * @return bool
     */
    protected function isAggregator(Crawler $crawler): bool
    {
        $title = strtolower($crawler->filter('title')->text(''));
        $articleCount = $crawler->filter('article, section')->count();
        $headingCount = $crawler->filter('h2, h3')->count();

        return str_contains($title, 'best') || str_contains($title, 'top') || $articleCount >= 5 || $headingCount >= 8;
    }

    /**
     * Aggregator-style strategy: scan all content blocks.
     *
     * @param Crawler $crawler
     * @return RestaurantDTO[]
     */
    protected function detectFromMultipleBlocks(Crawler $crawler): array
    {
        $results = [];

        // Get all possible containers
        $crawler->filter('article, section, div, li')->each(function (Crawler $node) use (&$results) {
            $name = $this->detector->detectName($node);
            $address = $this->detector->detectAddress($node);

            if ($name || $address) {
                $results[] = new RestaurantDTO($name, $address);
            }
        });

        return $results;
    }

    /**
     * Single restaurant strategy: try <title>, footer, and specific sections.
     *
     * @param Crawler $crawler
     * @return RestaurantDTO[]
     */
    protected function detectFromSinglePage(Crawler $crawler): array
    {
        $name = null;
        $address = null;

        // Try to extract from title
        $titleText = $crawler->filter('title')->text(null);
        if ($titleText) {
            $name = $this->detector->detectName(new Crawler($titleText));
        }

        // Try common footer-like sections
        $footerCandidates = $crawler->filter('footer, [id*=visit], [id*=contact], [class*=visit], [class*=contact]');
        foreach ($footerCandidates as $footerNode) {
            $footerCrawler = new Crawler($footerNode);
            $foundAddress = $this->detector->detectAddress($footerCrawler);
            if ($foundAddress) {
                $address = $foundAddress;
                break;
            }
        }

        if ($name || $address) {
            return [new RestaurantDTO($name, $address)];
        }

        return [];
    }
}

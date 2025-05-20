<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProxyScraperService;

class ScrapeProxies extends Command
{
    protected $signature = 'proxies:scrape';

    protected $description = 'Scrape fresh proxies and save them into storage';

    protected ProxyScraperService $proxyScraper;

    public function __construct(ProxyScraperService $proxyScraper)
    {
        parent::__construct();
        $this->proxyScraper = $proxyScraper;
    }

    public function handle()
    {
        $this->info('Scraping fresh proxies...');

        $proxies = $this->proxyScraper->scrapeProxies();

        if (empty($proxies)) {
            $this->error('No proxies found!');
            return 1; // failure code
        }

        $this->proxyScraper->saveProxiesToFile($proxies);

        $this->info('Successfully saved ' . count($proxies) . ' proxies.');

        return 0; // success code
    }
}
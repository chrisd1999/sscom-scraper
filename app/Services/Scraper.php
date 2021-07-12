<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Weidner\Goutte\GoutteFacade as Goutte;

class Scraper
{

    private const FieldTypes = [
        'BRAND'       => 0,
        'MODEL'       => 1,
        'YEAR'        => 2,
        'ENGINE_SIZE' => 3,
        'PRICE'       => 4,
    ];

    private ?string $url;
    private ?string $brand;
    private Crawler $crawler;

    public function __construct(?string $url, Crawler $crawler = null, string $brand = null)
    {
        $this->url = $url;
        $this->brand = $brand;
        $this->crawler = $crawler ?? Goutte::request('GET', $url);
    }

    public function scrapeSinglePage(): Collection
    {
        $tableNodeChildrens = $this->crawler->filterXpath('//*[@id="filter_frm"]/table[2]')->children('tr');

        $result = collect();

        $tableNodeChildrens->each(function (Crawler $node) use ($result) {

            // Check if valid advertisement, first or last table row does not have location.
            if ($node->filter('td .ads_region')->count() <= 0) {
                return;
            }

            $fields = $this->getMotorcycleFields($node);

            $result->push([
                'ss_id'             => $node->attr('id'),
                'ss_href'           => 'https://ss.com' . $node->filter('td a')->attr('href'),
                'ss_img'            => $node->filter('td a img')->attr('src'),
                'short_description' => $node->filter('td div a')->text(),
                'brand'             => $fields[self::FieldTypes["BRAND"]],
                'model'             => $fields[self::FieldTypes["MODEL"]],
                'year'              => $fields[self::FieldTypes["YEAR"]],
                'engine_size'       => $fields[self::FieldTypes["ENGINE_SIZE"]],
                'price'             => $fields[self::FieldTypes["PRICE"]],
                'location'          => $node->filter('td .ads_region')->text(),
            ]);
        });

        return $result;
    }

    public function createPageLinks(bool $includeFirst = false): array
    {
        $pages = $this->getPageNumbers();
        $pageLinks = $includeFirst ? ["{$this->url}"] : [];

        for ($num = 2; $num <= $pages; $num++) {
            $link = "{$this->url}page{$num}.html";
            array_push($pageLinks, $link);
        }

        return $pageLinks;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
        $this->crawler = Goutte::request('GET', $url);
    }

    private function getPageNumbers(): int
    {
        $table = $this->crawler->filterXpath('//*[@id="filter_frm"]/table[3]');

        return $table->filter('.navi')->count() - 1;
    }

    private function getMotorcycleFields(Crawler $node): array
    {
        if ($this->brand) {
            $fields = $node->filter('.pp6')->each(
                fn (Crawler $node, $i) => $node->text()
            );
            array_unshift($fields, $this->brand);

            return $fields;
        }

        return $node->filter('.pp6')->each(
            fn (Crawler $node, $i) => $node->text()
        );
    }
}

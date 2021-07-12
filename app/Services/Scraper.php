<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Weidner\Goutte\GoutteFacade as Goutte;

class Scraper {

    private const FieldTypes = [
        'BRAND'       => 0,
        'MODEL'       => 1,
        'YEAR'        => 2,
        'ENGINE_SIZE' => 3,
        'PRICE'       => 4,
    ];

    public static function scrapeSinglePage(string $url) : Collection
    {
        $crawler = Goutte::request('GET', $url);
        $tableNodeChildrens = $crawler->filterXpath('//*[@id="filter_frm"]/table[2]')->children('tr');

        $result = collect();

        $tableNodeChildrens->each(function(Crawler $node) use ($result) {

            // Check if valid advertisement, first or last table row does not have location.
            if ($node->filter('td .ads_region')->count() <= 0) {
                return;
            }

            $fields = self::getMotorcycleFields($node);

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


    private static function getMotorcycleFields(Crawler $node): array
    {
        return $node->filter('.pp6')->each(
            fn (Crawler $node, $i) => $node->text()
        );
    }
}
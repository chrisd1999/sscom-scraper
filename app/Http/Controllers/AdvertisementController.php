<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdvertisementCollection;
use App\Http\Resources\AdvertisementResource;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Config;
use Symfony\Component\DomCrawler\Crawler;
use Weidner\Goutte\GoutteFacade as Goutte;

class AdvertisementController extends Controller
{
    private const FieldTypes = [
        'BRAND'       => 0,
        'MODEL'       => 1,
        'YEAR'        => 2,
        'ENGINE_SIZE' => 3,
        'PRICE'       => 4,
    ];

    public function index(): AdvertisementCollection
    {
        return new AdvertisementCollection(Advertisement::all());
    }

    public function show($id): AdvertisementResource
    {
        // return new AdvertisementResource(Advertisement::where('ss_id', $ss_id)->first());
        return new AdvertisementResource(Advertisement::findOrFail($id));
    }

    public function store(): \Illuminate\Http\JsonResponse
    {
        $url = Config::get('constants.url.last_2days');

        $crawler = Goutte::request('GET', $url);
        $tableNodeChildrens = $crawler->filterXpath('//*[@id="filter_frm"]/table[2]')->children('tr');

        $tableNodeChildrens->each(function (Crawler $node) {

            // Continue each loop, if there is not a valid advertisement, e.g 
            // first or last row.
            if ($node->filter('td .ads_region')->count() <= 0) {
                return;
            }

            $ss_id = $node->attr('id');
            if (Advertisement::where('ss_id', $ss_id)->first()) {
                return false;
            }

            $fields = $this->getMotorcycleFields($node);

            Advertisement::create([
                'ss_id'             => $ss_id,
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
        return response()->json(['status' => 201]);
    }

    private function getMotorcycleFields(Crawler $node): array
    {
        return $node->filter('.pp6')->each(
            fn (Crawler $node, $i) => $node->text()
        );
    }
}

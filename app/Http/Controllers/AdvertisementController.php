<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdvertisementCollection;
use App\Http\Resources\AdvertisementResource;
use App\Models\Advertisement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Symfony\Component\DomCrawler\Crawler;
use Weidner\Goutte\GoutteFacade as Goutte;

class AdvertisementController extends Controller
{
    private const FIELDS = [
        'brand',
        'model',
        'year',
        'engine_size',
        'price',
    ];

    public function index()
    {
        return new AdvertisementCollection(Advertisement::all());
    }

    public function show($id)
    {
        // return new AdvertisementResource(Advertisement::where('ss_id', $ss_id)->first());
        return new AdvertisementResource(Advertisement::findOrFail($id));
    }

    public function store()
    {
        $url = Config::get('constants.url.last_2days');

        $crawler = Goutte::request('GET', $url);
        $tableNodeChildrens = $crawler->filterXpath('//*[@id="filter_frm"]/table[2]')->children('tr');

        $tableNodeChildrens->each(function (Crawler $node) {
            if (
                $node->filter('td .ads_region')->count() <= 0
                || Advertisement::where('ss_id', $node->attr('id'))->first() !== null
            ) {
                return;
            }

            $fields = $this->getMotorcycleFields($node);

            Advertisement::create([
                'ss_id' => $node->attr('id'),
                'ss_href' => 'https://ss.com' . $node->filter('td a')->attr('href'),
                'ss_img' => $node->filter('td a img')->attr('src'),
                'short_description' => $node->filter('td div a')->text(),
                'brand' => $fields['brand'],
                'model' => $fields['model'],
                'year' => $fields['year'],
                'engine_size' => $fields['engine_size'],
                'price' => $fields['price'],
                'location' => $node->filter('td .ads_region')->text(),
            ]);
        });
        return response()->json(['status' => 201]);
    }

    private function getMotorcycleFields(Crawler $node): Collection
    {
        $motorcycle = collect([]);

        $node->filter('.pp6')->each(
            fn (Crawler $node, $i) => $motorcycle->put(self::FIELDS[$i], $node->text())
        );

        return $motorcycle;
    }
}

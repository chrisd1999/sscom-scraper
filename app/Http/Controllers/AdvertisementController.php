<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdvertisementCollection;
use App\Http\Resources\AdvertisementResource;
use App\Jobs\CreateAdvertisement;
use App\Models\Advertisement;
use App\Services\Scraper;
use Illuminate\Support\Facades\Config;
use Symfony\Component\DomCrawler\Crawler;
use Weidner\Goutte\GoutteFacade as Goutte;

class AdvertisementController extends Controller
{
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
        $url = Config::get('constants.url.motorcycles.last_2days');
        $pageData = (new Scraper($url))->scrapeSinglePage($url);

        CreateAdvertisement::dispatch($pageData);

        return response()->json(['status' => 200]);
    }
}

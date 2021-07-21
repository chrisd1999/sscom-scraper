<?php

namespace App\Http\Controllers;

use App\Jobs\CreateAdvertisement;
use App\Services\Scraper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class AdvertisementAllController extends Controller
{
    public function store()
    {
        $urls = collect(Config::get('constants.url.motorcycles.brand_urls'));

        $urls->each(function ($url, $key) {

            $scraper = new Scraper($url, null, $key);
            $links = $scraper->createPageLinks();

            CreateAdvertisement::dispatch($this->scrapeAllAdvertPages($scraper, $links));
        });

        return back()->with('msg', 'success');
    }

    private function scrapeAllAdvertPages(Scraper $scraper, array $links): Collection
    {
        $data = $scraper->scrapeSinglePage();

        foreach ($links as $link) {
            $scraper->setUrl($link);
            $scraper->scrapeSinglePage()->map(function ($page) use ($data) {
                $data->push($page);
            });
        }

        return $data;
    }

    // private function createNewAdvertisement(Collection $pageData): void
    // {
    //     $pageData->each(function ($entry) {
    //         if (Advertisement::where('ss_id', $entry['ss_id'])->first()) {
    //             return;
    //         }

    //         Advertisement::create($entry);
    //     });
    // }
}

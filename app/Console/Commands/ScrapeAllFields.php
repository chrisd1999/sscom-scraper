<?php

namespace App\Console\Commands;

use App\Http\Controllers\AdvertisementAllController;
use Illuminate\Console\Command;

class ScrapeAllFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape all adverts data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new AdvertisementAllController)->store();
        return 0;
    }
}

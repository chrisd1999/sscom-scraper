<?php

namespace App\Console\Commands;

use App\Http\Controllers\AdvertisementController;
use Illuminate\Console\Command;

class ScrapeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:2days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape last 2 days adverts';

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
        (new AdvertisementController)->store();
        return 0;
    }
}

<?php

namespace App\Jobs;

use App\Models\Advertisement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CreateAdvertisement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Collection $pageData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $pageData)
    {
        $this->pageData = $pageData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->pageData->each(function ($entry) {
            if (Advertisement::where('ss_id', $entry['ss_id'])->first()) {
                return;
            }

            Advertisement::create($entry);
        });
    }
}

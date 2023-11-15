<?php

namespace App\Listeners;

use App\Events\CampaignStopped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class CampaignStoppedListener
{
    public function handle(CampaignStopped $event)
    {
        Log::channel('development')->alert('CampaignStoppedListener');

        $campaignId = $event->campaign->id;

        Artisan::call("queue:clear --queue=campaign_$campaignId");
    }
}



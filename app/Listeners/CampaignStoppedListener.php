<?php

namespace App\Listeners;

use App\Events\CampaignStopped;
use App\Models\CampaignProspect;
use App\Models\CampaignStepProspect;
use App\Models\EmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class CampaignStoppedListener
{
    public function handle(CampaignStopped $event)
    {
        Log::channel('development')->alert('CampaignStoppedListener');
        $jobs = EmailJob::where("campaign_id", $event->campaign->id)->get();
        foreach ($jobs as $job) {
            $job->job->delete();
        }

        CampaignStepProspect::where('status', 'scheduled')->where('campaign_id', $event->campaign->id)->update(['status' => 'pending']);
    }
}



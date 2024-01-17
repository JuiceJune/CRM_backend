<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignProspect;
use App\Models\CampaignSentProspect;
use App\Models\CampaignStepProspect;
use App\Models\CampaignStepVersion;
use App\Models\Prospect;
use App\Services\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $prospect;
    public $campaign;
    public $step;
    public $version;

    public function __construct(Prospect $prospect, CampaignStepVersion $version)
    {
        $this->prospect = $prospect;
        $this->version = $version;
        $this->step = $this->version->step;
        $this->campaign = $this->step->campaign;
    }
    public function handle()
    {
        Log::channel('development')->alert("Campaign Id:" . $this->campaign->id . "Step: " . $this->step->step . "Version: " . $this->version->version . " | Email send to " . $this->prospect["id"]);
        $currentDateTime = \Carbon\Carbon::now($this->campaign->timezone);


        //        (new \App\Services\SendEmail)->send($this->campaign, $this->prospect);

        CampaignStepProspect::where('campaign_id', $this->campaign->id)->where('prospect_id', $this->prospect->id)->where('campaign_step_id', $this->step->id)->update(['status' => "sent"]);

        $campaignSentProspect = CampaignSentProspect::create([
            'campaign_step_version_id' => $this->version->id,
            'campaign_step_id' => $this->step->id,
            'campaign_id' => $this->campaign->id,
            'prospect_id' => $this->prospect->id,
            'sent_time' => $currentDateTime,
            'status' => $currentDateTime,
        ]);

        //TODO add CampaignProspectMessage

        $nextStep = $this->campaign->step($this->step->step + 1);
        $startAfter = $this->step->start_after;
        $nextSendingTime = $currentDateTime;
        if($startAfter["time_type"] === "days") {
//            $nextSendingTime->addDays($startAfter["time"]);
            $nextSendingTime->addMinutes(2);
        }

        if($nextStep) {
            CampaignProspect::where('campaign_id', $this->campaign->id)->where('prospect_id', $this->prospect->id)->update(['step' => $this->step->step + 1]);
            CampaignStepProspect::create([
                'campaign_id' => $this->campaign->id,
                'campaign_step_id' => $nextStep->id,
                'prospect_id' => $this->prospect->id,
                'available_at' => $nextSendingTime,
            ]);
        } else {
            CampaignProspect::where('campaign_id', $this->campaign->id)->where('prospect_id', $this->prospect->id)->update(['status' => 'end']);
        }


        $id = $this->job->getJobId();
    }
}


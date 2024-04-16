<?php

namespace App\Services\Mail;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\CampaignStep;
use App\Models\CampaignStepVersion;
use App\Models\Prospect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SetupMailService
{
    private CampaignMessage $campaignMessage;
    private Prospect $prospect;
    private CampaignStepVersion $version;
    private CampaignStep $step;
    private Campaign $campaign;
    private Carbon $currentDateTime;
    public function __construct(CampaignMessage $campaignMessage)
    {
        $this->campaignMessage = $campaignMessage;
        $this->prospect = $campaignMessage->prospect;
        $this->version = $campaignMessage->campaignStepVersion;
        $this->step = $campaignMessage->campaignStep;
        $this->campaign = $campaignMessage->campaign;
        $this->currentDateTime = Carbon::now($this->campaign->timezone);
    }
    public function setup()
    {
        Log::alert('Send email to: ' . $this->prospect->email);
    }
}

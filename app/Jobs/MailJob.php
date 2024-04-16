<?php

namespace App\Jobs;

use App\Models\CampaignMessage;
use App\Services\Mail\SetupMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public CampaignMessage $campaignMessage;

    public function __construct(CampaignMessage $campaignMessage)
    {
        $this->campaignMessage = $campaignMessage;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new SetupMailService($this->campaignMessage))->setup();
    }
}

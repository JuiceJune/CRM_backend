<?php

namespace App\Services\CampaignServices;

use App\Services\CampaignJobServices\CampaignRedisJobService;
use Illuminate\Support\Facades\Log;
use App\Models\Campaign;
use Exception;

class StopCampaignService {

    public Campaign $campaign;
    private CampaignRedisJobService $campaignJobService;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->campaignJobService = new CampaignRedisJobService();
    }

    public function stopCampaign(): void
    {
        try {
            $this->campaignJobService->deleteCampaignJobs($this->campaign);
        } catch (Exception $error) {
            Log::error('StopCampaign: ' . $error->getMessage());
        }
    }
}

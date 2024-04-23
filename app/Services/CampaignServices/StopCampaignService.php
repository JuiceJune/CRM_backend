<?php

namespace App\Services\CampaignServices;

use App\Services\CampaignJobServices\CampaignJobService;
use Illuminate\Support\Facades\Log;
use App\Models\Campaign;
use Exception;

class StopCampaignService {

    public Campaign $campaign;
    private CampaignJobService $campaignJobService;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->campaignJobService = new CampaignJobService();
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

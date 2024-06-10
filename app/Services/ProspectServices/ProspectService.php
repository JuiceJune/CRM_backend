<?php

namespace App\Services\ProspectServices;

use App\Models\Campaign;
use App\Models\CampaignProspect;
use App\Models\Prospect;
use App\Services\CampaignJobServices\CampaignRedisJobService;
use Google\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProspectService
{
    private Prospect $prospect;
    private Campaign $campaign;

    public function __construct(Prospect $prospect, Campaign $campaign)
    {
        $this->prospect = $prospect;
        $this->campaign = $campaign;
    }

    public function getProspect(): Prospect
    {
        return $this->prospect;
    }

    public function setProspect(Prospect $prospect): void
    {
        $this->prospect = $prospect;
    }

    public function changeStatus(string $status)
    {
        try {
            switch ($status) {
                case 'active':
                    break;
                case 'inactive':
                    return $this->setStatusInactive();
                    break;
                case "opened":
                    break;
            }
        } catch (Exception $exception) {
            Log::error('ProspectService ChangeStatus: ' . $exception->getMessage());
        }
    }

    public function setStatusActive()
    {
        try {

        } catch (Exception $exception) {
            Log::error('ProspectService ChangeStatus: ' . $exception->getMessage());
        }
    }

    public function setStatusInactive(): int
    {
        DB::beginTransaction();
        try {
            $jobService = new CampaignRedisJobService();
            $jobService->deleteProspectJobs($this->campaign, $this->prospect);

            $this->prospect->update(["status" => "inactive"]);
            CampaignProspect::query()->where('campaign_id', $this->campaign->id)
                ->where('prospect_id', $this->prospect->id)->update(["status" => "inactive"]);
            $this->prospect->campaignMessages()->whereIn('status', ['pending', 'scheduled'])->update(["status" => "inactive"]);

            DB::commit();
            return 1;
        } catch (Exception $exception) {
            Log::error('ProspectService setStatusInactive: ' . $exception->getMessage());
            DB::rollBack();
            return 0;
        }
    }
}

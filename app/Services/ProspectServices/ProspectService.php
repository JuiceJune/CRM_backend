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

    public function changeStatus(string $status): int
    {
        try {
            return match ($status) {
                'active' => $this->setStatusActive(),
                'inactive' => $this->setStatusInactive(),
                "responded" => $this->setStatusResponded(),
                "bounced" => $this->setStatusBounced(),
                "unsubscribed" => $this->setStatusUnsubscribed(),
                default => 0,
            };
        } catch (Exception $exception) {
            Log::error('ProspectService ChangeStatus: ' . $exception->getMessage());
            return 0;
        }
    }

    public function setStatusActive(): int
    {
        DB::beginTransaction();
        try {
            $this->prospect->update(["status" => "active"]);
            CampaignProspect::query()->where('campaign_id', $this->campaign->id)
                ->where('prospect_id', $this->prospect->id)->update(["status" => "active"]);
            $this->prospect->campaignMessages()->whereIn('status', ['inactive'])->update(["status" => "pending"]);

            DB::commit();
            return 1;
        } catch (Exception $exception) {
            Log::error('ProspectService ChangeStatus: ' . $exception->getMessage());
            DB::rollBack();
            return 0;
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

    public function setStatusResponded(): int
    {
        DB::beginTransaction();
        try {
            $jobService = new CampaignRedisJobService();
            $jobService->deleteProspectJobs($this->campaign, $this->prospect);

            $this->prospect->update(["status" => "responded"]);
            CampaignProspect::query()->where('campaign_id', $this->campaign->id)
                ->where('prospect_id', $this->prospect->id)->update(["status" => "responded"]);
            $lastMessage = $this->prospect->campaignMessages()
                ->whereNotIn('status', ['pending', 'scheduled'])
                ->orderBy('id', 'desc')
                ->first();

            $lastMessage?->update(["status" => "responded"]);

            $this->prospect->campaignMessages()
                ->whereIn('status', ['pending', 'scheduled'])
                ->update(['status', 'inactive']);

            DB::commit();
            return 1;
        } catch (Exception $exception) {
            Log::error('ProspectService setStatusResponded: ' . $exception->getMessage());
            DB::rollBack();
            return 0;
        }
    }

    public function setStatusBounced(): int
    {
        DB::beginTransaction();
        try {
            $jobService = new CampaignRedisJobService();
            $jobService->deleteProspectJobs($this->campaign, $this->prospect);

            $this->prospect->update(["status" => "bounced"]);
            CampaignProspect::query()->where('campaign_id', $this->campaign->id)
                ->where('prospect_id', $this->prospect->id)->update(["status" => "bounced"]);
            $lastMessage = $this->prospect->campaignMessages()
                ->whereNotIn('status', ['pending', 'scheduled'])
                ->orderBy('id', 'desc')
                ->first();

            $lastMessage?->update(["status" => "bounced"]);

            $this->prospect->campaignMessages()
                ->whereIn('status', ['pending', 'scheduled'])
                ->update(['status', 'inactive']);

            DB::commit();
            return 1;
        } catch (Exception $exception) {
            Log::error('ProspectService setStatusBounced: ' . $exception->getMessage());
            DB::rollBack();
            return 0;
        }
    }

    public function setStatusUnsubscribed(): int
    {
        DB::beginTransaction();
        try {
            $jobService = new CampaignRedisJobService();
            $jobService->deleteProspectJobs($this->campaign, $this->prospect);

            $this->prospect->update(["status" => "unsubscribed"]);
            CampaignProspect::query()->where('campaign_id', $this->campaign->id)
                ->where('prospect_id', $this->prospect->id)->update(["status" => "unsubscribed"]);
            $lastMessage = $this->prospect->campaignMessages()
                ->whereNotIn('status', ['pending', 'scheduled'])
                ->orderBy('id', 'desc')
                ->first();

            $lastMessage?->update(["status" => "unsubscribed"]);

            $this->prospect->campaignMessages()
                ->whereIn('status', ['pending', 'scheduled'])
                ->update(['status', 'inactive']);

            DB::commit();
            return 1;
        } catch (Exception $exception) {
            Log::error('ProspectService setStatusUnsubscribed: ' . $exception->getMessage());
            DB::rollBack();
            return 0;
        }
    }
}

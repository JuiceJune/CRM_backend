<?php

namespace App\Services\ProspectServices;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\Project;
use App\Models\Prospect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StoreProspectService
{
    public function storeProspects(array $prospects, Campaign $campaign, int $accountId): array
    {
        try {
            $firstStep = $campaign->step(1);
            $version = $firstStep->version('A');
            $timezone = $campaign->timezone;
            $dateInTimeZone = Carbon::now($timezone);
            $project = $campaign->project;

            $duplicateProspects = [];
            $successProspects = [];

            foreach ($prospects as $prospect) {
                if ($this->isDuplicate($prospect, $project->id, $accountId)) {
                    $duplicateProspects[] = $prospect;
                    continue;
                }

                $createdProspect = $this->createProspect($prospect, $accountId);
                $this->attachProspectToCampaignAndProject($campaign, $project, $createdProspect, $accountId);
                $this->createCampaignMessage($campaign, $firstStep, $version, $createdProspect, $accountId, $dateInTimeZone);

                $successProspects[] = $createdProspect;
            }

            return [
                'successProspects' => $successProspects,
                'duplicateProspects' => $duplicateProspects,
            ];
        } catch (\Exception $exception) {
            Log::error("storeProspects | " . $exception->getMessage());
            throw new \Error($exception->getMessage());
        }
    }

    private function isDuplicate(array $prospect, int $projectId, int $accountId): bool
    {
        try {
            $prospects = Prospect::where('email', $prospect['email'])->where('account_id', $accountId)->get();
            foreach ($prospects as $prospect) {
                if($prospect->existsInProject($projectId)) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $exception) {
            Log::error("isDuplicate | " . $exception->getMessage());
            throw new \Error($exception->getMessage());
        }
    }

    private function createProspect(array $prospect, int $accountId): Prospect
    {
        try {
            $prospect['account_id'] = $accountId;
            $prospect['status'] = $prospect['status'] ?: 'active';
            return Prospect::create($prospect);
        } catch (\Exception $exception) {
            Log::error("createProspect | " . $exception->getMessage());
            throw new \Error($exception->getMessage());
        }
    }

    private function attachProspectToCampaignAndProject(Campaign $campaign, Project $project, Prospect $prospect, int $accountId): void
    {
        try {
            $campaign->prospects()->attach($prospect->id, ['account_id' => $accountId]);
            $project->prospects()->attach($prospect->id, ['account_id' => $accountId]);
        } catch (\Exception $exception) {
            Log::error("attachProspectToCampaignAndProject | " . $exception->getMessage());
            throw new \Error($exception->getMessage());
        }
    }

    private function createCampaignMessage(Campaign $campaign, $firstStep, $version, Prospect $prospect, int $accountId, Carbon $dateInTimeZone): void
    {
        try {
            CampaignMessage::query()->create([
                'account_id' => $accountId,
                'campaign_id' => $campaign->id,
                'campaign_step_id' => $firstStep->id,
                'campaign_step_version_id' => $version->id,
                'prospect_id' => $prospect->id,
                'available_at' => $dateInTimeZone,
            ]);
        } catch (\Exception $exception) {
            Log::error("createCampaignMessage | " . $exception->getMessage());
            throw new \Error($exception->getMessage());
        }
    }
}

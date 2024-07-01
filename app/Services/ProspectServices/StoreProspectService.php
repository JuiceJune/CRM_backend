<?php

namespace App\Services\ProspectServices;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\Project;
use App\Models\Prospect;
use Carbon\Carbon;

class StoreProspectService
{
    public function processProspects(array $prospects, Campaign $campaign, int $accountId): array
    {
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
            $this->attachProspectToCampaign($campaign, $project, $createdProspect, $accountId);
            $this->createCampaignMessage($campaign, $firstStep, $version, $createdProspect, $accountId, $dateInTimeZone);

            $successProspects[] = $createdProspect;
        }

        return [
            'successProspects' => $successProspects,
            'duplicateProspects' => $duplicateProspects,
        ];
    }

    private function isDuplicate(array $prospect, int $projectId, int $accountId): bool
    {
        $prospects = Prospect::where('email', $prospect['email'])->where('account_id', $accountId)->get();
        foreach ($prospects as $prospect) {
            if($prospect->existsInProject($projectId)) {
                return true;
            }
        }
        return false;
    }

    private function createProspect(array $prospect, int $accountId): Prospect
    {
        $prospect['account_id'] = $accountId;
        $prospect['status'] = $prospect['status'] ?: 'active';
        return Prospect::create($prospect);
    }

    private function attachProspectToCampaign(Campaign $campaign, Project $project, Prospect $prospect, int $accountId)
    {
        $campaign->prospects()->attach($prospect->id, ['account_id' => $accountId]);
        $project->prospects()->attach($prospect->id, ['account_id' => $accountId]);
    }

    private function createCampaignMessage(Campaign $campaign, $firstStep, $version, Prospect $prospect, int $accountId, Carbon $dateInTimeZone)
    {
        CampaignMessage::query()->create([
            'account_id' => $accountId,
            'campaign_id' => $campaign->id,
            'campaign_step_id' => $firstStep->id,
            'campaign_step_version_id' => $version->id,
            'prospect_id' => $prospect->id,
            'available_at' => $dateInTimeZone,
        ]);
    }
}

<?php

namespace App\Http\Resources\Campaign;

use App\Services\CampaignServices\StatisticCampaignService;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignsTableInProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $statisticCampaignService = new StatisticCampaignService($this->resource);
        $deliveredAllTime = $statisticCampaignService->deliveredAllTime();
        $openedAllTime = $statisticCampaignService->openedAllTime();
        $respondedAllTime = $statisticCampaignService->respondedAllTime();

        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'mailbox' => $this->mailbox ? $this->mailbox->email : null,
            'status' => $this->status,
            'prospectsCount' => $this->prospects_count,
            'deliveredAllTimeCount' => $deliveredAllTime,
            'openedAllTimeCount' => $openedAllTime,
            'respondedAllTimeCount' => $respondedAllTime,
        ];
    }
}

<?php

namespace App\Http\Resources\Campaign;

use App\Http\Resources\CampaignStep\CampaignStepResource;
use App\Http\Resources\Mailbox\MailboxCampaignCreateResource;
use App\Http\Resources\Mailbox\MailboxResource;
use App\Services\CampaignServices\StatisticCampaignService;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignShowResource extends JsonResource
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
        $sentAllTime = $statisticCampaignService->sentAllTime();
        $invalidAllTime = $statisticCampaignService->invalidAllTime();
        $bouncedAllTime = $statisticCampaignService->bouncedAllTime();

        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'mailbox' => $this->mailbox ? new MailboxCampaignCreateResource($this->mailbox) : null,
            'status' => $this->status,
            'timezone' => $this->timezone,
            'start_date' => $this->start_date,
            'steps' => CampaignStepResource::collection($this->steps),
            'send_limit' => $this->send_limit,

            'prospectsCount' => $this->prospects_count,
            'deliveredAllTimeCount' => $deliveredAllTime,
            'openedAllTimeCount' => $openedAllTime,
            'respondedAllTimeCount' => $respondedAllTime,
            'sentAllTimeCount' => $sentAllTime,
            'invalidAllTimeCount' => $invalidAllTime,
            'bouncedAllTimeCount' => $bouncedAllTime,
        ];
    }
}

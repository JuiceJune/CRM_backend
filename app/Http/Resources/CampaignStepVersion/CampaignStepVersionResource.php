<?php

namespace App\Http\Resources\CampaignStepVersion;

use App\Services\VersionServices\StatisticVersionService;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignStepVersionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $statisticVersionService = new StatisticVersionService($this->resource);
        $deliveredAllTime = $statisticVersionService->deliveredAllTime();
        $openedAllTime = $statisticVersionService->openedAllTime();
        $respondedAllTime = $statisticVersionService->respondedAllTime();
        $sentAllTime = $statisticVersionService->sentAllTime();
        $invalidAllTime = $statisticVersionService->invalidAllTime();
        $bouncedAllTime = $statisticVersionService->bouncedAllTime();
        $queuedNow = $statisticVersionService->queuedNow();
        $unsubscribeAllTime = $statisticVersionService->unsubscribeAllTime();

        return [
            'id' => $this->uuid,
            'version' => $this->version,
            'status' => $this->status,

            'deliveredAllTimeCount' => $deliveredAllTime,
            'openedAllTimeCount' => $openedAllTime,
            'respondedAllTimeCount' => $respondedAllTime,
            'sentAllTimeCount' => $sentAllTime,
            'invalidAllTimeCount' => $invalidAllTime,
            'bouncedAllTimeCount' => $bouncedAllTime,
            'queuedNowCount' => $queuedNow,
            'unsubscribeAllTimeCount' => $unsubscribeAllTime,
        ];
    }
}

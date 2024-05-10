<?php

namespace App\Http\Resources\CampaignStep;

use App\Http\Resources\CampaignStepVersion\CampaignStepVersionResource;
use App\Services\StepServices\StatisticStepService;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignStepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $statisticStepService = new StatisticStepService($this->resource);
        $deliveredAllTime = $statisticStepService->deliveredAllTime();
        $openedAllTime = $statisticStepService->openedAllTime();
        $respondedAllTime = $statisticStepService->respondedAllTime();
        $sentAllTime = $statisticStepService->sentAllTime();
        $invalidAllTime = $statisticStepService->invalidAllTime();
        $bouncedAllTime = $statisticStepService->bouncedAllTime();
        $queuedNow = $statisticStepService->queuedNow();
        $unsubscribeAllTime = $statisticStepService->unsubscribeAllTime();

        return [
            'id' => $this->uuid,
            'step' => $this->step,
            'versions' => CampaignStepVersionResource::collection($this->versions),

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

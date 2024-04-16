<?php

namespace App\Http\Resources\CampaignStep;

use App\Http\Resources\CampaignStepVersion\CampaignStepVersionResource;
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
        return [
            'id' => $this->uuid,
            'step' => $this->step,
            'campaign' => $this->campaign,
            'sending_time_json' => $this->sending_time_json,
            'reply_to_exist_thread' => $this->reply_to_exist_thread,
            'period' => $this->period,
            'start_after' => $this->start_after,
            'versions' => CampaignStepVersionResource::collection($this->versions),
            'prospects' => $this->campaignStepProspects,
        ];
    }
}

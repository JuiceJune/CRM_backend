<?php

namespace App\Http\Resources\Campaign;

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
            'id' => $this->id,
            'step' => $this->step,
            'campaign' => $this->campaign,
            'sending_time_json' => $this->sending_time_json,
            'period' => $this->period,
            'start_after' => $this->start_after,
            'versions' => CampaignStepVersionResource::collection($this->versions),
            'prospects' => $this->campaignStepProspects,
        ];
    }
}

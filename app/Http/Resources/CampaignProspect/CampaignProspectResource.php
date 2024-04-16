<?php

namespace App\Http\Resources\CampaignProspect;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProspectResource extends JsonResource
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
            'uuid' => $this->uuid,
            'campaign_id' => $this->campaign_id,
            'prospect_id' => $this->prospect_id,
            'step' => $this->step,
            'status' => $this->status,
        ];
    }
}

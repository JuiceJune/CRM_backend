<?php

namespace App\Http\Resources\Campaign;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignStepProspectResource extends JsonResource
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
            'campaign' => $this->campaign,
            'step' => $this->step,
            'prospect' => $this->prospect,
            'status' => $this->status,
            'available_at' => $this->available_at,
        ];
    }
}

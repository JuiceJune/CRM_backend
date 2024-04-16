<?php

namespace App\Http\Resources\CampaignStepVersion;

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
        return [
            'id' => $this->uuid,
            'campaign_step' => $this->campaign_step,
            'subject' => $this->subject,
            'message' => $this->message,
            'version' => $this->version,
        ];
    }
}

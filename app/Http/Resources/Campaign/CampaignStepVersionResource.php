<?php

namespace App\Http\Resources\Campaign;

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
            'id' => $this->id,
//            'campaign_step' => $this->campaignStep,
            'subject' => $this->subject,
            'message' => $this->message,
            'version' => $this->version,
        ];
    }
}

<?php

namespace App\Http\Resources\Campaign;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignSentProspectResource extends JsonResource
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
            'version' => $this->version,
            'prospect' => $this->prospect,
            'status' => $this->status,
            'sent_time' => $this->sent_time,
            'responses' => $this->responses,
        ];
    }
}

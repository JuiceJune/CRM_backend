<?php

namespace App\Http\Resources\Prospect;

use Illuminate\Http\Resources\Json\JsonResource;

class ProspectCampaignResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'status' => $this->prospect_status_in_campaign,
            'step' => $this->step,
        ];
    }
}

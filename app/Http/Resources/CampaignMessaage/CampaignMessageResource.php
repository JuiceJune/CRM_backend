<?php

namespace App\Http\Resources\CampaignMessaage;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignMessageResource extends JsonResource
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
            'campaign_step_id' => $this->campaign_step_id,
            'campaign_step_version_id' => $this->campaign_step_version_id,
            'prospect_id' => $this->prospect_id,
            'status' => $this->status,
            'available_at' => $this->available_at,
            'sent_time' => $this->sent_time,
            'message_id' => $this->message_id,
            'message_string_id' => $this->message_string_id,
            'thread_id' => $this->thread_id,
            'subject' => $this->subject,
            'message' => $this->message,
            'from' => $this->from,
            'to' => $this->to,
            'type' => $this->type,
        ];
    }
}

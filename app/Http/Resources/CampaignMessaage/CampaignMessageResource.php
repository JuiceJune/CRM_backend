<?php

namespace App\Http\Resources\CampaignMessaage;

use App\Http\Resources\MessageActivity\MessageActivityResource;
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
            'campaign_step_id' => $this->campaignStep->step,
            'campaign_step_version_id' => $this->campaignStepVersion->version,
            'status' => $this->status,
            'sent_time' => $this->sent_time,
            'subject' => $this->subject,
            'message' => $this->message,
            'to' => $this->to,
            'activities' => MessageActivityResource::collection($this->messageActivities)
        ];
    }
}

<?php

namespace App\Http\Resources\Campaign;

use App\Http\Resources\CampaignStep\CampaignStepResource;
use App\Http\Resources\Mailbox\MailboxCampaignCreateResource;
use App\Http\Resources\Mailbox\MailboxResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignEditResource extends JsonResource
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
            'name' => $this->name,
            'mailbox' => new MailboxCampaignCreateResource($this->mailbox),
            'status' => $this->status,
            'timezone' => $this->timezone,
            'start_date' => $this->start_date,
            'steps' => CampaignStepResource::collection($this->steps),
            'send_limit' => $this->send_limit,
        ];
    }
}

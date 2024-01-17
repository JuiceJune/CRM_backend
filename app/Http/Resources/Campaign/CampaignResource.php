<?php

namespace App\Http\Resources\Campaign;

use App\Http\Resources\MailboxResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'name' => $this->name,
            'mailbox' => new MailboxResource($this->mailbox),
            'project' => $this->project->name,
            'status' => $this->status,
            'timezone' => $this->timezone,
            'start_date' => $this->start_date,
            'steps' => CampaignStepResource::collection($this->steps),
            'prospects' => $this->prospects,
            'send_limit' => $this->send_limit,
            'priority_config' => $this->priority_config,
        ];
    }
}

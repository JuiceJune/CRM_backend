<?php

namespace App\Http\Resources;

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
            'subject' => $this->subject,
            'message' => $this->message,
        ];
    }
}

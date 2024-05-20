<?php

namespace App\Http\Resources\Mailbox;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailboxProjectsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'signature' => $this->signature,
            'send_limit' => $this->send_limit,
            'emailProvider' => $this->email_provider,
            'activeCampaignsCount' => $this->campaigns()->where('status', 'active')->count()
        ];
    }
}

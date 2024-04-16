<?php

namespace App\Http\Resources\Mailbox;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailboxCampaignCreateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $campaigns = $this->campaigns();
        $available_limit = $this->send_limit;
        foreach ($campaigns as $campaign) {
            $available_limit -= $campaign->send_limit;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'send_limit' => $this->send_limit,
            'signature' => $this->signature,
            'available_limit' => $available_limit,
        ];
    }
}

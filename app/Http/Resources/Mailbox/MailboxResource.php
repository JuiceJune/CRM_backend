<?php

namespace App\Http\Resources\Mailbox;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailboxResource extends JsonResource
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
            'domain' => $this->domain,
            'avatar' => $this->avatar,
            'expires_in' => $this->expires_in,
            'send_limit' => $this->send_limit,
            'signature' => $this->signature,
            'status' => $this->status,
            'scopes' => $this->scopes,
            'last_token_refresh' => $this->last_token_refresh,
            'errors' => $this->errors,
            'email_provider' => $this->email_provider,
        ];
    }
}

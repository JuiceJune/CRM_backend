<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Mailbox\MailboxResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->uuid,
            'logo' => $this->logo,
            'name' => $this->name,
            'client' => new ClientResource($this->client),
            'mailboxes' => MailboxResource::collection($this->mailboxes),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];
    }
}

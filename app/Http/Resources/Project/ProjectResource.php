<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Mailbox\MailboxProjectsResource;
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
            'mailboxes' => MailboxProjectsResource::collection($this->mailboxes),
            'campaignsCount' => $this->campaigns ? count($this->campaigns) : 0
        ];
    }
}

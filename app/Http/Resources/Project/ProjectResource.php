<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Campaign\CampaignsTableInProjectResource;
use App\Http\Resources\Mailbox\MailboxProjectsResource;
use App\Http\Resources\User\UserCreateResource;
use App\Http\Resources\User\UserResource;
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
            'status' => $this->status,
            'creator' => new UserResource($this->creator),
            'mailboxes' => MailboxProjectsResource::collection($this->mailboxes),
            'users' => UserCreateResource::collection($this->users),
            'campaigns' => CampaignsTableInProjectResource::collection($this->campaigns)
        ];
    }
}

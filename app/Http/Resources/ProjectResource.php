<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $csm = $this->usersWithPosition("CSM") ? $this->usersWithPosition("CSM")->first() : null;
        $sdrs = $this->usersWithPosition("SDR") ? $this->usersWithPosition("SDR") : null;
        $researchers = $this->usersWithPosition("Researcher") ? $this->usersWithPosition("Researcher") : null;
        $research_manager = $this->usersWithPosition("Research Manager") ? $this->usersWithPosition("Research Manager")->first() : null;
        $it_specialist = $this->usersWithPosition("IT Specialist") ? $this->usersWithPosition("IT Specialist")->first() : null;

        return [
            'id' => $this->id,
            'logo' => $this->logo,
            'name' => $this->name,
            'client' => new ClientResource($this->client),

            'mailboxes' => $this->mailboxes ? MailboxResource::collection($this->mailboxes) : null,
            'linkedin_accounts' => $this->linkedin_accounts ? LinkedinResource::collection($this->linkedin_accounts) : null,

            'csm' => $csm ? new UserResource($csm) : null,
            'research_manager' => $research_manager ? new UserResource($research_manager) : null,
            'it_specialist' => $it_specialist ? new UserResource($it_specialist) : null,
            'sdrs' => $sdrs ? UserResource::collection($sdrs) : null,
            'researchers' => $researchers ? UserResource::collection($researchers) : null,

            'start_date' => $this->start_date,
            'period' => $this->period,
            'price' => $this->price,
        ];
    }
}

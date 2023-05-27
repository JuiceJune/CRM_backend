<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LinkedinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $project = $this->projects ? $this->projects->first() : null;
        $csm = ( $project && $project->usersWithPosition("CSM") ) ? $project->usersWithPosition("CSM")->first() : null;
        $sdrs = ( $project && $project->usersWithPosition("SDR") ) ? $project->usersWithPosition("SDR") : null;
        $it_specialist = ( $project && $project->usersWithPosition("IT Specialist") ) ? $project->usersWithPosition("IT Specialist")->first() : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->mailbox ? $this->mailbox->email : null,
            'link' => $this->link,
            'avatar' => $this->avatar,
            'project' => $project,
            'csm' => $csm ? new UserResource($csm) : null,
            'it_specialist' => $it_specialist ? new UserResource($it_specialist) : null,
            'sdrs' => $sdrs ? UserResource::collection($sdrs) : null,
            'password' => $this->password,
            'create_date' => $this->create_date,
        ];
    }
}

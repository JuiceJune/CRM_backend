<?php

namespace App\Http\Resources;

use App\Models\Linkedin;
use Illuminate\Http\Resources\Json\JsonResource;

class MailboxResource extends JsonResource
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
            'email' => $this->email,
            'name' => $this->name,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'domain' => $this->domain,
            'password' => $this->password,
            'app_password' => $this->app_password,
            'for_linkedin' => $this->for_linkedin,
            'project' => $project,
            'csm' => $csm ? new UserResource($csm) : null,
            'it_specialist' => $it_specialist ? new UserResource($it_specialist) : null,
            'sdrs' => $sdrs ? UserResource::collection($sdrs) : null,
            'linkedin_link' => $this->linkedin ? $this->linkedin->link : null,
            'email_provider' => $this->email_provider,
            'email_provider_logo' => $this->email_provider->logo,
            'create_date' => $this->create_date,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

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
            'domain' => $this->domain,
            'avatar' => $this->avatar,
            'phone' => $this->phone,
            'signature' => $this->signature,
            'password' => $this->password,
            'app_password' => $this->app_password,
            'email_provider' => $this->email_provider,
            'project' => $project,
            'csm' => $csm ? new UserResource($csm) : null,
            'it_specialist' => $it_specialist ? new UserResource($it_specialist) : null,
            'sdrs' => $sdrs ? UserResource::collection($sdrs) : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

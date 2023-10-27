<?php

namespace App\Http\Resources;

use App\Models\Linkedin;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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

        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'name' => $this->name,
            'email' => $this->email,
            'location' => $this->location,
            'industry' => $this->industry,
            'start_date' => $this->start_date,
            'project' => $project,
        ];
    }
}

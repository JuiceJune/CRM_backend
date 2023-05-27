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
            'logo' => $this->logo,
            'email' => $this->email,
            'name' => $this->name,
            'project' => $project,
            'start_date' => $this->start_date,
            'location' => $this->location,
            'industry' => $this->industry,
            'company' => $this->company,
        ];
    }
}

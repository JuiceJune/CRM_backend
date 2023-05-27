<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'projects' => $this->projects,
            'avatar' => $this->avatar,
            'role' => $this->role,
            'birthday' => $this->birthday,
            'location' => $this->location,
            'position' => $this->position->title,
            'start_date' => $this->start_date,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

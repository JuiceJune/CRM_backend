<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Position\PositionResource;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'role' => new RoleResource($this->role),
            'position' => new PositionResource($this->position),
            'projects' => ProjectResource::collection($this->projects),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

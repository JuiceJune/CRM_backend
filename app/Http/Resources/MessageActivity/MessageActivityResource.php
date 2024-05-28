<?php

namespace App\Http\Resources\MessageActivity;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date_time' => $this->date_time,
            'type' => $this->type,
            'ip' => $this->ip,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProspectResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'status' => $this->status,
            'company' => $this->company,
            'website' => $this->website,
            'linkedin_url' => $this->linkedin_url,
            'date_contacted' => $this->date_contacted,
            'date_responded' => $this->date_responded,
            'date_added' => $this->date_added,
            'phone' => $this->phone,
            'title' => $this->title,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'timezone' => $this->timezone,
            'industry' => $this->industry,
            'tags' => $this->tags,
            'snippet_1' => $this->snippet_1,
            'snippet_2' => $this->snippet_2,
            'snippet_3' => $this->snippet_3,
            'snippet_4' => $this->snippet_4,
            'snippet_5' => $this->snippet_5,
            'snippet_6' => $this->snippet_6,
            'snippet_7' => $this->snippet_7,
            'snippet_8' => $this->snippet_8,
            'snippet_9' => $this->snippet_9,
            'snippet_10' => $this->snippet_10,
            'snippet_11' => $this->snippet_11,
            'snippet_12' => $this->snippet_12,
            'snippet_13' => $this->snippet_13,
            'snippet_14' => $this->snippet_14,
            'snippet_15' => $this->snippet_15,
            'campaigns' => $this->campaigns,
            'step' => $this->step,
            'prospect_status_in_campaign' => $this->prospect_status_in_campaign,
        ];
    }
}

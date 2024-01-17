<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Prospect extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'status',
        'company',
        'website',
        'linkedin_url',
        'date_contacted',
        'date_responded',
        'date_added',
        'phone',
        'title',
        'address',
        'city',
        'state',
        'country',
        'timezone',
        'industry',
        'tags',
        'snippet_1',
        'snippet_2',
        'snippet_3',
        'snippet_4',
        'snippet_5',
        'snippet_6',
        'snippet_7',
        'snippet_8',
        'snippet_9',
        'snippet_10',
        'snippet_11',
        'snippet_12',
        'snippet_13',
        'snippet_14',
        'snippet_15',
    ];

    protected $casts = [
        'tags' => 'json'
    ];

    public function emailJob(): HasMany
    {
        return $this->hasMany(EmailJob::class);
    }

    public function campaignStepProspects(): HasMany
    {
        return $this->hasMany(CampaignStepProspect::class);
    }

    public function campaignSentProspects(): HasMany
    {
        return $this->hasMany(CampaignSentProspect::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaigns_prospects', 'prospect_id', 'campaign_id')
            ->withPivot('step', 'status');
    }
}

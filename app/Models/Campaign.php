<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mailbox_id',
        'project_id',
        'status',
        'timezone',
        'start_date',
        'send_limit',
        'priority_config'
    ];

    protected $casts = [
        'priority_config' => 'json',
    ];

    protected $attributes = [
        'priority_config' => '{
        1: {1: 100},
        2: {1: 20, 2: 80},
        3: {1: 20, 2: 40, 3: 40},
        4: {1: 20, 2: 26, 3: 26, 4: 26},
        5: {1: 20, 2: 20, 3: 20, 4: 20, 5: 20},
        6: {1: 20, 2: 16, 3: 16, 4: 16, 5: 16, 6: 16},
        7: {1: 20, 2: 13, 3: 13, 4: 13, 5: 13, 6: 13, 7: 13},
        8: {1: 20, 2: 11, 3: 11, 4: 11, 5: 11, 6: 11, 7: 11, 8: 11},
        9: {1: 20, 2: 10, 3: 10, 4: 10, 5: 10, 6: 10, 7: 10, 8: 10, 9: 10},
        10: {1: 20, 2: 9, 3: 9, 4: 9, 5: 9, 6: 9, 7: 9, 8: 9, 9: 9, 10: 9},
    }'
    ];

    public function prospects() {
        return $this->belongsToMany(Prospect::class, 'campaigns_prospects', 'campaign_id', 'prospect_id')->withPivot('step as step', 'status as prospect_status_in_campaign');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(CampaignStep::class);
    }

    public function step($step)
    {
        return $this->steps->where('step', $step)->first();
    }

    public function stepById($id)
    {
        return $this->steps->find($id);
    }

    public function mailbox()
    {
        return $this->belongsTo(Mailbox::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function prospectsByStatus($status)
    {
        return $this->prospects->where('campaign_status', $status);
    }

    public function prospectsByNonStatus($status)
    {
        return $this->prospects->where('campaign_status', '!=', $status);
    }

    public function prospectsByStep($step)
    {
        return $this->prospects->where('step', $step);
    }

    public function prospectsByStepAndStatus($step, $status, $limit)
    {
        return $this->prospects
            ->where('step', $step)
            ->where('prospect_status_in_campaign', $status)
            ->take($limit)
            ->values()
            ->toArray();
    }

    public function emailJob(): HasMany
    {
        return $this->hasMany(EmailJob::class);
    }

    public function campaignSentProspects(): HasMany
    {
        return $this->hasMany(CampaignSentProspect::class);
    }

    public function campaignStepProspects(): HasMany
    {
        return $this->hasMany(CampaignStepProspect::class);
    }
}

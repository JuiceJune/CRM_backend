<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignStep extends Model
{
    use HasFactory;

    protected $table = "campaign_steps";

    protected $fillable = [
        'step',
        'campaign_id',
        'sending_time_json',
        'period',
        'start_after',
    ];

    protected $casts = [
        'sending_time_json' => 'json',
        'start_after' => 'json'
    ];

    protected $attributes = [
        'sending_time_json' => '{
        "Mon": [true, "08:00", "15:00"],
        "Tues": [true, "08:00", "15:00"],
        "Wed": [true, "08:00", "15:00"],
        "Thurs": [true, "08:00", "15:00"],
        "Fri": [true, "08:00", "15:00"],
        "Sat": [false, "08:00", "15:00"],
        "Sun": [false, "08:00", "15:00"]
    }',
        'start_after' => '{
        "time": 3,
        "time_type": "days"
    }',
    ];

    public function version($version)
    {
        return $this->versions->where('version', $version)->first();
    }

    public function versionById($id)
    {
        return $this->versions->find($id);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(CampaignStepVersion::class);
    }

    public function campaign(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

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
}

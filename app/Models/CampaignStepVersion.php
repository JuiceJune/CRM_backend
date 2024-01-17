<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignStepVersion extends Model
{
    use HasFactory;

    protected $table = "campaign_step_versions";

    protected $fillable = [
        'campaign_step_id',
        'subject',
        'message',
        'version',
    ];

    public function sentProspects(): HasMany
    {
        return $this->hasMany(CampaignSentProspect::class);
    }

    public function step(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CampaignStep::class, 'campaign_step_id');
    }

    public function emailJob(): HasMany
    {
        return $this->hasMany(EmailJob::class);
    }
}

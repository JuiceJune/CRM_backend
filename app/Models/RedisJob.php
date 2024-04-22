<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedisJob extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'uuid',
        'account_id',
        'type',
        'redis_job_id',
        'campaign_id',
        'prospect_id',
        'campaign_step_id',
        'campaign_step_version_id',
        'status',
        'date_time',
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function campaign(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function prospect(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function campaignMessage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CampaignMessage::class, 'redis_job_id');
    }


    public function campaignStep(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CampaignStep::class);
    }

    public function campaignStepVersion(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CampaignStepVersion::class);
    }
}

<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignMessage extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "campaign_messages";

    protected $fillable = [
        'account_id',
        'campaign_id',
        'campaign_step_id',
        'campaign_step_version_id',
        'prospect_id',
        'status',
        'available_at',
        'sent_time',
        'message_id',
        'message_string_id',
        'thread_id',
        'subject',
        'message',
        'from',
        'to',
        'type',
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function campaign(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function campaignStep(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CampaignStep::class, 'campaign_step_id');
    }

    public function campaignStepVersion(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CampaignStepVersion::class, 'campaign_step_id');
    }

    public function prospect(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Prospect::class, 'prospect_id');
    }

    public function scheduled(): void
    {
        $this->update(['status', 'scheduled']);
    }
}

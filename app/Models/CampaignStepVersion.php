<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignStepVersion extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "campaign_step_versions";

    protected $fillable = [
        'account_id',
        'status',
        'campaign_step_id',
        'subject',
        'message',
        'version',
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function step(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CampaignStep::class, 'campaign_step_id');
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignMessage::class);
    }
}

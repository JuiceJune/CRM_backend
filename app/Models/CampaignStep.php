<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignStep extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "campaign_steps";

    protected $fillable = [
        'account_id',
        'step',
        'campaign_id',
        'sending_time_json',
        'period',
        'start_after',
        'reply_to_exist_thread',
    ];

    protected $casts = [
        'sending_time_json' => 'json',
        'reply_to_exist_thread' => 'json',
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
        'reply_to_exist_thread' => '{
            "reply": false,
            "step": null
        }',
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function version($version)
    {
        return $this->versions->where('version', $version)->first();
    }

    public function versions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignStepVersion::class);
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignMessage::class);
    }

    public function campaign(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}

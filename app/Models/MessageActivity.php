<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageActivity extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "message_activities";

    protected $fillable = [
        'campaign_message_id',
        'date_time',
        'type',
        'ip'
    ];

    public function message(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CampaignMessage::class, 'campaign_message_id');
    }
}

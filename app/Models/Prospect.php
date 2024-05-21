<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'account_id',
        'first_name',
        'last_name',
        'email',
        'status',
        'company',
        'website',
        'linkedin_url',
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

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaigns_prospects', 'prospect_id', 'campaign_id')
            ->withPivot('step', 'status');
    }

    public function campaignMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignMessage::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Account extends Model
{
    use HasFactory, UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function campaignMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignMessage::class);
    }

    public function campaignProspects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignProspect::class);
    }

    public function campaignsSteps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignStep::class);
    }

    public function campaignStepVersions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignStepVersion::class);
    }

    public function clients(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function mailboxes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Mailbox::class);
    }

    public function projects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function prospects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Prospect::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function redisJobs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RedisJob::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailJob extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'campaign_step_version_id',
        'campaign_step_id',
        'campaign_id',
        'prospect_id',
    ];

    protected $table = 'emails_jobs';

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function version()
    {
        return $this->belongsTo(CampaignStepVersion::class);
    }

    public function step()
    {
        return $this->belongsTo(CampaignStep::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }
}

<?php

namespace App\Services\StepServices;

use App\Models\CampaignStep;
use Illuminate\Support\Facades\Log;

class StatisticStepService
{
    public CampaignStep $step;
    public function __construct(CampaignStep $step)
    {
        $this->step = $step;
    }

    public function sentAllTime(): int
    {
        try {
            return $this->step->messages()
                ->where('type', 'from me')
                ->whereNotIn('status', ['pending', 'scheduled'])
                ->count();
        } catch (\Exception $error) {
            Log::error('SentAllTime: ' . $error->getMessage());
            return 0;
        }
    }

    public function deliveredAllTime(): int
    {
        try {
            return $this->step->messages()
                ->where('type', 'from me')
                ->whereNotIn('status', ['pending', 'scheduled', 'bounced'])
                ->count();
        } catch (\Exception $error) {
            Log::error('DeliveredAllTime: ' . $error->getMessage());
            return 0;
        }
    }

    public function invalidAllTime(): int
    {
        try {
            return $this->step->messages()
                ->where('type', 'from me')
                ->whereNotIn('status', ['invalid'])
                ->count();
        } catch (\Exception $error) {
            Log::error('InvalidAllTime: ' . $error->getMessage());
            return 0;
        }
    }

    public function bouncedAllTime(): int
    {
        try {
            return $this->step->messages()
                ->where('type', 'from me')
                ->whereNotIn('status', ['bounced'])
                ->count();
        } catch (\Exception $error) {
            Log::error('BouncedAllTime: ' . $error->getMessage());
            return 0;
        }
    }

    public function openedAllTime(): int
    {
        try {
            return $this->step->messages()
                ->where('type', 'from me')
                ->whereNotIn('status', ['pending', 'scheduled', 'sent', 'bounced'])
                ->count();
        } catch (\Exception $error) {
            Log::error('OpenedAllTime: ' . $error->getMessage());
            return 0;
        }
    }

    public function respondedAllTime(): int
    {
        try {
            return $this->step->messages()
                ->where('type', 'from me')
                ->where('status', 'replayed')
                ->count();
        } catch (\Exception $error) {
            Log::error('RespondedAllTime: ' . $error->getMessage());
            return 0;
        }
    }

    public function queuedNow(): int
    {
        try {
            return $this->step->messages()
                ->where('type', 'from me')
                ->where('status', 'scheduled')
                ->count();
        } catch (\Exception $error) {
            Log::error('QueuedNow: ' . $error->getMessage());
            return 0;
        }
    }

    public function unsubscribeAllTime(): int
    {
        try {
            return $this->step->messages()
                ->where('type', 'from me')
                ->where('status', 'unsubscribe')
                ->count();
        } catch (\Exception $error) {
            Log::error('UnsubscribeAllTime: ' . $error->getMessage());
            return 0;
        }
    }
}

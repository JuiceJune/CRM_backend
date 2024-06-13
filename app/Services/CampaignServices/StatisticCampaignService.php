<?php

namespace App\Services\CampaignServices;

use App\Models\Campaign;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;

class StatisticCampaignService
{
    public Campaign $campaign;
    private Carbon $dateTime;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->dateTime = Carbon::now($campaign->timezone);
    }

    public function sentAllTime(): int
    {
        try {
            return $this->campaign->campaignMessages()
                ->where('type', 'from me')
                ->whereNotIn('status', ['pending', 'scheduled'])
                ->count();
        } catch (\Exception $error) {
            Log::error('SentAllTime: ' . $error->getMessage());
            return 0;
        }
    }

    public function sentTime(DateTime $from, DateTime $to): int
    {
        try {
            return $this->campaign->campaignMessages()
                ->where('type', 'from me')
                ->whereNotIn('status', ['pending', 'scheduled'])
                ->whereBetween('sent_time', [$from, $to])
                ->count();
        } catch (\Exception $error) {
            Log::error('SentAllTime: ' . $error->getMessage());
            return 0;
        }
    }

    public function deliveredAllTime(): int
    {
        try {
            return $this->campaign->campaignMessages()
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
            return $this->campaign->campaignMessages()
                ->where('type', 'from me')
                ->where('status', 'invalid')
                ->count();
        } catch (\Exception $error) {
            Log::error('InvalidAllTime: ' . $error->getMessage());
            return 0;
        }
    }

    public function bouncedAllTime(): int
    {
        try {
            return $this->campaign->campaignMessages()
                ->where('type', 'from me')
                ->where('status', 'bounced')
                ->count();
        } catch (\Exception $error) {
            Log::error('BouncedAllTime: ' . $error->getMessage());
            return 0;
        }
    }

    public function openedAllTime(): int
    {
        try {
            return $this->campaign->campaignMessages()
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
            return $this->campaign->campaignMessages()
                ->where('type', 'from me')
                ->where('status', 'replayed')
                ->count();
        } catch (\Exception $error) {
            Log::error('RespondedAllTime: ' . $error->getMessage());
            return 0;
        }
    }
}

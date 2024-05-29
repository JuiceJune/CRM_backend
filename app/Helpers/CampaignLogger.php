<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class CampaignLogger
{
    public static function log($campaignId, $level, $message, array $context = [])
    {
        Log::channel('campaign')->withContext(['campaign_id' => $campaignId])->$level($message, $context);
    }
}

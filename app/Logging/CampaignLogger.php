<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class CampaignLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        Log::alert('FFF_2: ' . json_encode($config));

        $campaignId = $config['campaign_id'] ?? 'default';
        $logPath = storage_path("logs/campaigns/{$campaignId}/campaign.log");

        $logger = new Logger('campaign');
        $logger->pushHandler(new StreamHandler($logPath));

        return $logger;
    }
}

<?php

namespace App\Logging;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CampaignLogger
{
    public static function log($campaignId, $message)
    {
        $logPath = storage_path("logs/campaigns/{$campaignId}");

        // Перевірка, чи існує папка для кампанії. Якщо ні - створіть її.
        if (!File::exists($logPath)) {
            if (!File::makeDirectory($logPath, 0755, true)) {
                Log::error("Failed to create directory for campaign logs: {$logPath}");
                return;
            }
        }

        $logFile = $logPath . '/' . now()->format('Y-m-d') . '.log';

        // Запис тексту в файл логу.
        if (!File::append($logFile, '[' . now()->format('Y-m-d H:i:s') . '] ' . $message . PHP_EOL)) {
            Log::error("Failed to write to log file: {$logFile}");
            return;
        }
    }
}

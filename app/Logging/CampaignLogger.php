<?php

namespace App\Logging;

use Illuminate\Support\Facades\File;

class CampaignLogger
{
    public static function log($campaignId, $message)
    {
        $logPath = storage_path("logs/campaigns/{$campaignId}");

        // Перевірка, чи існує папка для кампанії. Якщо ні - створіть її.
        if (!File::exists($logPath)) {
            File::makeDirectory($logPath, 0755, true);
        }

        $logFile = $logPath . '/' . now()->format('Y-m-d') . '.log';

        // Запис тексту в файл логу.
        File::append($logFile, '[' . now()->format('Y-m-d H:i:s') . '] ' . $message . PHP_EOL);
    }
}

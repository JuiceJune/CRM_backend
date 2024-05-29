<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class CampaignLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Log::extend('campaign', function ($app, array $config) {
            Log::alert('FFF_3: ' . json_encode($app));
            Log::alert('FFF_3: ' . json_encode($config));

            return new \Monolog\Logger('campaign', [
                new \Monolog\Handler\StreamHandler(storage_path('logs/campaigns/') . $config['campaign_id'] . '.log')
            ]);
        });
    }
}

<?php

namespace App\Providers;

use App\Events\CampaignStarted;
use App\Events\CampaignStopped;
use App\Events\QueueStarted;
use App\Listeners\CampaignStartedListener;
use App\Listeners\CampaignStoppedListener;
use App\Listeners\QueueStartedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class
        ],
        CampaignStarted::class => [
            CampaignStartedListener::class,
        ],
        CampaignStopped::class => [
            CampaignStoppedListener::class,
        ],
        QueueStarted::class => [
            QueueStartedListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}

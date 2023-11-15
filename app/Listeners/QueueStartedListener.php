<?php

namespace App\Listeners;

use App\Events\QueueStarted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;

class QueueStartedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function viaQueue(QueueStarted $event): string
    {
        return $event->queueName;
    }

    public function handle(QueueStarted $event)
    {
        $queueName = $event->queueName;
        Artisan::call("queue:work --queue=$queueName");
    }
}


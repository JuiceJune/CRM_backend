<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class QueueStarted implements ShouldBroadcast
{
    use Dispatchable;

    public $queueName;

    public function __construct(string $queueName)
    {
        $this->queueName = $queueName;
    }

    public function __invoke(){
        //
    }

    public function broadcastOn()
    {
        return new PrivateChannel($this->queueName);
    }
}


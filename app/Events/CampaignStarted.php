<?php

namespace App\Events;

use App\Models\Campaign;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Log;

class CampaignStarted implements ShouldBroadcast
{
    use Dispatchable;

    public $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function __invoke()
    {
        return 'START';
    }

    public function broadcastOn()
    {
        return new PrivateChannel($this->campaign->id);
    }
}


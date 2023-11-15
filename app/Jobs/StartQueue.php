<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class StartQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queueName;

    public function __construct(String $queueName)
    {
        $this->queueName = $queueName;
    }
    public function handle()
    {
        Log::channel('development')->alert('StartQueue');
        Artisan::call("queue:work --queue=$this->queueName");
    }
}


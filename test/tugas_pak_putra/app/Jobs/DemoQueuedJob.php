<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DemoQueuedJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue('demo');
    }

    public function handle(): void
    {
        // Demo job for queue routing / testing.
    }
}

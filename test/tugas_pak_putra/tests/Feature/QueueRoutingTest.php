<?php

namespace Tests\Feature;

use App\Jobs\DemoQueuedJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class QueueRoutingTest extends TestCase
{
    /**
     * Ensure job routes to the intended queue.
     */
    public function test_job_is_routed_to_queue_demo(): void
    {
        Queue::fake();

        dispatch(new DemoQueuedJob);

        Queue::assertPushedOn('demo', DemoQueuedJob::class);
    }
}

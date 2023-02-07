<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\JobsLog\Listeners;

use Illuminate\Queue\Events\JobProcessed;
use Umbrellio\EventTracker\Trackers\JobsLog\JobsLogTracker;

class TrackProcessedJobListener
{
    private $tracker;

    public function __construct(JobsLogTracker $tracker)
    {
        $this->tracker = $tracker;
    }

    public function handle(JobProcessed $event): void
    {
        $this->tracker->write($event->job, 'processed');
    }
}

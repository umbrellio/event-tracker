<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\JobsLog\Listeners;

use Illuminate\Queue\Events\JobProcessing;
use Umbrellio\EventTracker\Trackers\JobsLog\JobsLogTracker;

class TrackProcessingJobListener
{
    private $tracker;

    public function __construct(JobsLogTracker $tracker)
    {
        $this->tracker = $tracker;
    }

    public function handle(JobProcessing $event): void
    {
        $this->tracker->write($event->job, 'processing');
    }
}

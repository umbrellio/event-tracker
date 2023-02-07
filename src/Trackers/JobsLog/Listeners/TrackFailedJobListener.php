<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\JobsLog\Listeners;

use Illuminate\Queue\Events\JobFailed;
use Umbrellio\EventTracker\Trackers\JobsLog\JobsLogTracker;

class TrackFailedJobListener
{
    private $tracker;

    public function __construct(JobsLogTracker $tracker)
    {
        $this->tracker = $tracker;
    }

    public function handle(JobFailed $event): void
    {
        $this->tracker->write($event->job, 'failed');
    }
}

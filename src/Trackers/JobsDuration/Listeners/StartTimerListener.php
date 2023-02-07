<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\JobsDuration\Listeners;

use Illuminate\Queue\Events\JobProcessing;
use Umbrellio\EventTracker\Services\JobsFilter;
use Umbrellio\EventTracker\Services\Timer;

class StartTimerListener
{
    private $timer;
    private $filter;

    public function __construct(Timer $timer, JobsFilter $filter)
    {
        $this->timer = $timer;
        $this->filter = $filter;
    }

    public function handle(JobProcessing $event): void
    {
        if ($this->filter->needSkip($event->job)) {
            return;
        }

        $this->timer->start();
    }
}

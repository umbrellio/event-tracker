<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\JobsDuration\Listeners;

use Illuminate\Queue\Events\JobProcessed;
use Umbrellio\EventTracker\Services\Adapters\BaseAdapter;
use Umbrellio\EventTracker\Services\Timer;

class EndTimerListener
{
    private Timer $timer;
    private BaseAdapter $adapter;
    private array $metricConfig;

    public function __construct(Timer $timer, BaseAdapter $adapter, array $metricConfig)
    {
        $this->timer = $timer;
        $this->adapter = $adapter;
        $this->metricConfig = $metricConfig;
    }

    public function handle(JobProcessed $event): void
    {
        $job = $event->job;
        $time = $this->timer->end();

        if ($time === null) {
            return;
        }

        $this->adapter->write($this->metricConfig['measurement'], $time, [
            'jobName' => $job->resolveName(),
        ]);
    }
}

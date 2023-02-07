<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\JobsLog;

use Illuminate\Contracts\Queue\Job;
use Umbrellio\EventTracker\Services\Adapters\BaseAdapter;
use Umbrellio\EventTracker\Services\JobsFilter;

class JobsLogTracker
{
    private BaseAdapter $adapter;
    private JobsFilter $guard;
    private array $metricConfig;

    public function __construct(BaseAdapter $adapter, JobsFilter $filter, array $metricConfig)
    {
        $this->adapter = $adapter;
        $this->guard = $filter;
        $this->metricConfig = $metricConfig;
    }

    public function write(Job $job, string $eventName): void
    {
        if ($this->guard->needSkip($job)) {
            return;
        }

        $this->adapter->write($this->metricConfig['measurement'], '', [
            'jobName' => $job->resolveName(),
            'eventName' => $eventName,
        ]);
    }
}

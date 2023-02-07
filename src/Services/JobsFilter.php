<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Services;

use Illuminate\Contracts\Queue\Job;

class JobsFilter
{
    private $skipJobs;

    public function __construct(array $skipJobs)
    {
        $this->skipJobs = $skipJobs;
    }

    public function needSkip(Job $job): bool
    {
        return in_array($job->resolveName(), $this->skipJobs, true);
    }
}

<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\JobsLog;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Prometheus\Counter;
use Umbrellio\EventTracker\Services\EventSubscriber;
use Umbrellio\EventTracker\Services\JobsFilter;
use Umbrellio\EventTracker\Trackers\BaseInstaller;
use Umbrellio\EventTracker\Trackers\JobsLog\Listeners\TrackFailedJobListener;
use Umbrellio\EventTracker\Trackers\JobsLog\Listeners\TrackProcessedJobListener;
use Umbrellio\EventTracker\Trackers\JobsLog\Listeners\TrackProcessingJobListener;

class Installer extends BaseInstaller
{
    private const EVENT_LISTENER_MAP = [
        JobFailed::class => TrackFailedJobListener::class,
        JobProcessed::class => TrackProcessedJobListener::class,
        JobProcessing::class => TrackProcessingJobListener::class,
    ];

    private EventSubscriber $subscriber;

    public function __construct(EventSubscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function install(Application $app, string $connection, array $metricConfig): void
    {
        $app->singleton(JobsLogTracker::class, function () use ($metricConfig, $app, $connection) {
            $filter = new JobsFilter($metricConfig['skip_jobs']);
            $adapterClass = $this->resolveAdapter($connection, Counter::TYPE);

            return new JobsLogTracker($app->make($adapterClass, compact('metricConfig')), $filter, $metricConfig);
        });

        $this->subscriber->subscribe(self::EVENT_LISTENER_MAP);
    }
}

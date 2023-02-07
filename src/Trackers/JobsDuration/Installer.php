<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\JobsDuration;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Prometheus\Histogram;
use Umbrellio\EventTracker\Services\EventSubscriber;
use Umbrellio\EventTracker\Services\JobsFilter;
use Umbrellio\EventTracker\Services\Timer;
use Umbrellio\EventTracker\Trackers\BaseInstaller;
use Umbrellio\EventTracker\Trackers\JobsDuration\Listeners\EndTimerListener;
use Umbrellio\EventTracker\Trackers\JobsDuration\Listeners\StartTimerListener;

class Installer extends BaseInstaller
{
    private const EVENT_LISTENER_MAP = [
        JobProcessing::class => StartTimerListener::class,
        JobProcessed::class => EndTimerListener::class,
    ];

    private EventSubscriber $subscriber;
    private Timer $timer;

    public function __construct(EventSubscriber $subscriber, Timer $timer)
    {
        $this->subscriber = $subscriber;
        $this->timer = $timer;
    }

    public function install(Application $app, string $connection, array $metricConfig): void
    {
        $app->singleton(EndTimerListener::class, function () use ($metricConfig, $app, $connection) {
            $adapterClass = $this->resolveAdapter($connection, Histogram::TYPE);

            return new EndTimerListener($this->timer, $app->make(
                $adapterClass,
                compact('metricConfig')
            ), $metricConfig);
        });

        $app->singleton(StartTimerListener::class, function () use ($metricConfig) {
            $filter = new JobsFilter($metricConfig['skip_jobs']);

            return new StartTimerListener($this->timer, $filter);
        });

        $this->subscriber->subscribe(self::EVENT_LISTENER_MAP);
    }
}

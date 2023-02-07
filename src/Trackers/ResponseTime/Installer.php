<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\ResponseTime;

use Illuminate\Contracts\Foundation\Application;
use Prometheus\Histogram;
use Umbrellio\EventTracker\Trackers\BaseInstaller;

class Installer extends BaseInstaller
{
    public function install(Application $app, string $connection, array $metricConfig): void
    {
        $app->singleton(ResponseTimeTrackerMiddleware::class, function () use ($metricConfig, $app, $connection) {
            $adapterClass = $this->resolveAdapter($connection, Histogram::TYPE);

            return new ResponseTimeTrackerMiddleware($app->make($adapterClass, compact('metricConfig')), $metricConfig);
        });
    }
}

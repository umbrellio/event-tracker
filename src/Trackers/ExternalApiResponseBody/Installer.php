<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\ExternalApiResponseBody;

use Illuminate\Contracts\Foundation\Application;
use Prometheus\Counter;
use Umbrellio\EventTracker\Trackers\BaseInstaller;

class Installer extends BaseInstaller
{
    public function install(Application $app, string $connection, array $metricConfig): void
    {
        $app->singleton(GuzzleClientOnStatsCallbackCreator::class, function () use ($app, $connection, $metricConfig) {
            $adapterClass = $this->resolveAdapter($connection, Counter::TYPE);

            return new GuzzleClientOnStatsCallbackCreator(
                $app->make($adapterClass, compact('metricConfig')),
                $app->make(MessageBodyFieldsExtractor::class),
                $metricConfig
            );
        });
    }
}

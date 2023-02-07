<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\ExternalApiResponse;

use Illuminate\Contracts\Foundation\Application;
use Prometheus\Histogram;
use Umbrellio\EventTracker\Exceptions\IncorrectConfigException;
use Umbrellio\EventTracker\Trackers\BaseInstaller;

class Installer extends BaseInstaller
{
    public function install(Application $app, string $connection, array $metricConfig): void
    {
        if (!in_array($metricConfig['main_metric'], $metricConfig['metrics'], true)) {
            throw new IncorrectConfigException('Option main_metric must be in metrics list');
        }

        $app->singleton(GuzzleClientOnStatsCallbackCreator::class, function () use ($app, $connection, $metricConfig) {
            $adapterClass = $this->resolveAdapter($connection, Histogram::TYPE);

            return new GuzzleClientOnStatsCallbackCreator(
                $app->make($adapterClass, compact('metricConfig')),
                $app->make(RequestParamsSummator::class),
                $metricConfig
            );
        });
    }
}

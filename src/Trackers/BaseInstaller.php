<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers;

use Illuminate\Contracts\Foundation\Application;
use Prometheus\Counter;
use Prometheus\Gauge;
use Prometheus\Histogram;
use Prometheus\Summary;
use Umbrellio\EventTracker\Exceptions\InstallerDoesntHaveRequiredAdapterForConnection;
use Umbrellio\EventTracker\Services\Adapters\EventAdapter;
use Umbrellio\EventTracker\Services\Adapters\PrometheusCounterAdapter;
use Umbrellio\EventTracker\Services\Adapters\PrometheusGaugeAdapter;
use Umbrellio\EventTracker\Services\Adapters\PrometheusHistogramAdapter;
use Umbrellio\EventTracker\Services\Adapters\PrometheusSummaryAdapter;

abstract class BaseInstaller
{
    private const METRIC_TYPE_ADAPTER_MAP = [
        Counter::TYPE => PrometheusCounterAdapter::class,
        Gauge::TYPE => PrometheusGaugeAdapter::class,
        Histogram::TYPE => PrometheusHistogramAdapter::class,
        Summary::TYPE => PrometheusSummaryAdapter::class,
    ];

    abstract public function install(Application $app, string $connection, array $metricConfig): void;

    protected function resolveAdapter(string $connection, string $prometheusMetricType): string
    {
        if (in_array($connection, ['telegraf', 'influxdb'], true)) {
            return EventAdapter::class;
        }

        if ($connection === 'prometheus') {
            return self::METRIC_TYPE_ADAPTER_MAP[$prometheusMetricType];
        }

        throw new InstallerDoesntHaveRequiredAdapterForConnection($connection);
    }
}

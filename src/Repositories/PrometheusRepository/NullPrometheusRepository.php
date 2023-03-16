<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Repositories\PrometheusRepository;

class NullPrometheusRepository implements PrometheusRepositoryContract
{
    public function getMetrics(): array
    {
        return [];
    }

    /**
     * @param float|int $count
     */
    public function writeCounter(string $measurement, array $trackerLabels = [], $count = 1): void
    {
    }

    public function writeGauge(string $measurement, $value, array $trackerLabels = []): void
    {
    }

    public function writeHistogram(string $measurement, $value, array $trackerLabels = [], array $buckets = []): void
    {
    }

    public function writeSummary(
        string $measurement,
        $value,
        array $trackerLabels = [],
        int $maxAgeSeconds = 600,
        array $quantile = null
    ): void {
    }
}

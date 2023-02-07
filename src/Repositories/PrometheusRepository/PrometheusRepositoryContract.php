<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Repositories\PrometheusRepository;

interface PrometheusRepositoryContract
{
    public function getMetrics(): array;
    public function writeCounter(string $measurement, array $trackerLabels): void;
    public function writeGauge(string $measurement, $value, array $trackerLabels): void;
    public function writeHistogram(string $measurement, $value, array $trackerLabels, array $buckets): void;
    public function writeSummary(
        string $measurement,
        $value,
        array $trackerLabels,
        int $maxAgeSeconds,
        array $quantile
    ): void;
}

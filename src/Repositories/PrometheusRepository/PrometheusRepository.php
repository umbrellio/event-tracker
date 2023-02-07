<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Repositories\PrometheusRepository;

use Prometheus\CollectorRegistry;

class PrometheusRepository implements PrometheusRepositoryContract
{
    private CollectorRegistry $registry;
    private array $config;

    public function __construct(CollectorRegistry $registry, array $config)
    {
        $this->registry = $registry;
        $this->config = $config;
    }

    public function getMetrics(): array
    {
        return $this->registry->getMetricFamilySamples();
    }

    public function writeCounter(string $measurement, array $trackerLabels = []): void
    {
        $labels = $this->labels($trackerLabels);

        $this->registry->getOrRegisterCounter($this->namespace(), $measurement, '', array_keys($labels))
            ->inc($labels);
    }

    public function writeGauge(string $measurement, $value, array $trackerLabels = []): void
    {
        $labels = $this->labels($trackerLabels);

        $this->registry->getOrRegisterGauge($this->namespace(), $measurement, '', array_keys($labels))
            ->set($value, $labels);
    }

    public function writeHistogram(string $measurement, $value, array $trackerLabels = [], array $buckets = []): void
    {
        $labels = $this->labels($trackerLabels);

        $this->registry->getOrRegisterHistogram($this->namespace(), $measurement, '', array_keys($labels), $buckets)
            ->observe($value, $labels);
    }

    public function writeSummary(
        string $measurement,
        $value,
        array $trackerLabels = [],
        int $maxAgeSeconds = 600,
        array $quantile = null
    ): void {
        $labels = $this->labels($trackerLabels);

        $this
            ->registry
            ->getOrRegisterSummary($this->namespace(), $measurement, '', array_keys($labels), $maxAgeSeconds, $quantile)
            ->observe($value, $labels);
    }

    private function namespace(): string
    {
        return $this->config['measurement_prefix'] ?? '';
    }

    private function labels(array $trackerLabels): array
    {
        return array_merge($this->config['connections']['prometheus']['labels'] ?? [], $trackerLabels);
    }
}

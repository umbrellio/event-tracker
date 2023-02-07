<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Services\Adapters;

class PrometheusHistogramAdapter extends BasePrometheusAdapter
{
    public function write(string $measurement, $value = null, array $tags = []): void
    {
        $this->repository->writeHistogram($measurement, $value, $tags, $this->metricConfig['prom_buckets'] ?? []);
    }
}

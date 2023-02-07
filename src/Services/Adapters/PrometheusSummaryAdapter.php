<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Services\Adapters;

class PrometheusSummaryAdapter extends BasePrometheusAdapter
{
    private const MAX_AGE_SECONDS_DEFAULT = 600;

    public function write(string $measurement, $value = null, array $tags = []): void
    {
        $this->repository->writeSummary(
            $measurement,
            $value,
            $tags,
            $this->metricConfig['max_age_seconds'] ?? self::MAX_AGE_SECONDS_DEFAULT,
            $this->metricConfig['quantile'] ?? []
        );
    }
}

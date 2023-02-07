<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Services\Adapters;

class PrometheusGaugeAdapter extends BasePrometheusAdapter
{
    public function write(string $measurement, $value = null, array $tags = []): void
    {
        $this->repository->writeGauge($measurement, $value, $tags);
    }
}

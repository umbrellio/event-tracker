<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Services\Adapters;

class PrometheusCounterAdapter extends BasePrometheusAdapter
{
    public function write(string $measurement, $value = null, array $tags = []): void
    {
        $this->repository->writeCounter($measurement, $tags, $value);
    }
}

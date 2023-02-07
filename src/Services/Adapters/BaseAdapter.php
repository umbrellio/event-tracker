<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Services\Adapters;

abstract class BaseAdapter
{
    protected array $metricConfig;

    public function __construct(array $metricConfig)
    {
        $this->metricConfig = $metricConfig;
    }

    abstract public function write(string $measurement, $value = null, array $tags = []): void;
}

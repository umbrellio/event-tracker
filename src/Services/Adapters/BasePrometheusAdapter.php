<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Services\Adapters;

use Umbrellio\EventTracker\Repositories\PrometheusRepository\PrometheusRepositoryContract;

abstract class BasePrometheusAdapter extends BaseAdapter
{
    protected PrometheusRepositoryContract $repository;

    public function __construct(PrometheusRepositoryContract $repository, array $metricConfig)
    {
        $this->repository = $repository;

        parent::__construct($metricConfig);
    }
}

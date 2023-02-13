<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Repositories\PrometheusRepository;

use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Route;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;
use Umbrellio\EventTracker\Repositories\BaseRepositoryInstaller;

class Installer extends BaseRepositoryInstaller
{
    public function install(array $config): void
    {
        if (!$config['enabled']) {
            $this->app->singleton(PrometheusRepositoryContract::class, NullPrometheusRepository::class);

            return;
        }

        $this->app->singleton(PrometheusRepositoryContract::class, function () use ($config) {
            /** @var RedisManager $redisManager */
            $redisManager = $this->app->make(RedisManager::class);
            $connection = $config['connections']['prometheus']['redis'];
            $redis = $redisManager->connection($connection)
                ->client();

            $storage = Redis::fromExistingConnection($redis);
            $registry = new CollectorRegistry($storage, false);

            return new PrometheusRepository($registry, $config);
        });

        $promConfig = $config['connections']['prometheus'];

        $path = $promConfig['metricsRoute']['endpoint'];

        Route::get($path, [PrometheusController::class, 'metrics']);
    }
}

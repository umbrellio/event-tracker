<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Repositories\PrometheusRepository;

use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Route;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;
use Redis as NativeRedis;
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
            $redisConfig = $config['connections']['prometheus']['redis'];
            $redisManager = $this->app->make(RedisManager::class, [$this->app, $redisConfig['client'], $redisConfig]);
            $redis = $redisManager->connection('connection')->client();
            $redis->setOption(NativeRedis::OPT_PREFIX, '');

            $storage = Redis::fromExistingConnection($redis);
            $registry = new CollectorRegistry($storage, false);

            return new PrometheusRepository($registry, $config);
        });

        $promConfig = $config['connections']['prometheus'];

        $path = $promConfig['metricsRoute']['endpoint'];

        Route::get($path, [PrometheusController::class, 'metrics']);
    }
}

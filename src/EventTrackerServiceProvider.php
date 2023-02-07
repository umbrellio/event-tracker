<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker;

use Illuminate\Support\ServiceProvider;
use Umbrellio\EventTracker\Repositories\BaseRepositoryInstaller;
use Umbrellio\EventTracker\Repositories\EventRepository\Installer as EventRepositoryInstaller;
use Umbrellio\EventTracker\Repositories\PrometheusRepository\Installer as PrometheusRepositoryInstaller;
use Umbrellio\EventTracker\Trackers\BaseInstaller;
use Umbrellio\EventTracker\Trackers\ExternalApiResponse\Installer as ExternalApiResponseInstaller;
use Umbrellio\EventTracker\Trackers\JobsDuration\Installer as JobsDurationInstaller;
use Umbrellio\EventTracker\Trackers\JobsLog\Installer as JobsLogInstaller;
use Umbrellio\EventTracker\Trackers\ResponseTime\Installer as ResponseTimeInstaller;

class EventTrackerServiceProvider extends ServiceProvider
{
    private const TRACKER_NAMES_INSTALLER_MAP = [
        'jobs_duration' => JobsDurationInstaller::class,
        'jobs_log' => JobsLogInstaller::class,
        'response_time' => ResponseTimeInstaller::class,
        'external_api_response' => ExternalApiResponseInstaller::class,
    ];

    private const REPOSITORY_INSTALLER_CONNECTION_MAP = [
        'prometheus' => PrometheusRepositoryInstaller::class,
        'telegraf' => EventRepositoryInstaller::class,
        'influxdb' => EventRepositoryInstaller::class,
    ];

    public function boot()
    {
        $this->publishes([
            $this->eventTrackerConfigPath() => config_path('event_tracker.php'),
        ]);

        $this->mergeConfigFrom($this->eventTrackerConfigPath(), 'event_tracker');

        $this->installTrackers(config('event_tracker'));
    }

    public function register()
    {
        $config = config('event_tracker');

        if (!$config) {
            return;
        }

        $repositoryInstaller = self::REPOSITORY_INSTALLER_CONNECTION_MAP[$config['connection']];

        /** @var BaseRepositoryInstaller $repositoryInstaller */
        $repositoryInstaller = new $repositoryInstaller($this->app);

        $repositoryInstaller->install($config);
    }

    protected function eventTrackerConfigPath(): string
    {
        return __DIR__ . '/../config/event_tracker.php';
    }

    private function installTrackers(array $config): void
    {
        foreach ($config['trackers'] as $trackerName => $trackerConfig) {
            /** @var BaseInstaller $installer */
            $installer = $this->app->make(self::TRACKER_NAMES_INSTALLER_MAP[$trackerName]);
            $installer->install($this->app, $config['connection'], $trackerConfig);
        }
    }
}

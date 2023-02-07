<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Repositories\EventRepository;

use InfluxDB\Client as InfluxClient;
use InfluxDB\Driver\UDP;
use InvalidArgumentException;
use Umbrellio\EventTracker\Connections\InfluxDBConnection;
use Umbrellio\EventTracker\Connections\TelegrafConnection;
use Umbrellio\EventTracker\Repositories\BaseRepositoryInstaller;

class Installer extends BaseRepositoryInstaller
{
    public function install(array $config): void
    {
        switch ($config['connection']) {
            case 'telegraf':
                $connectionObj = $this->buildTelegraf($config['connections']['telegraf']);
                break;
            case 'influxdb':
                $connectionObj = $this->buildInfluxDB($config['connections']['influxdb']);
                break;
            default:
                throw new InvalidArgumentException($config['connection']);
        }

        $this->app->singleton(EventRepository::class, function () use ($connectionObj) {
            return new EventRepository($connectionObj, config('event_tracker'));
        });
    }

    private function buildTelegraf(array $connectionConfig): TelegrafConnection
    {
        $udp = new UDP($connectionConfig['host'], $connectionConfig['port']);

        return new TelegrafConnection($udp);
    }

    private function buildInfluxDB(array $connectionConfig): InfluxDBConnection
    {
        $client = new InfluxClient(
            $connectionConfig['host'],
            $connectionConfig['port'],
            $connectionConfig['username'],
            $connectionConfig['password'],
            $connectionConfig['ssl'],
            $connectionConfig['verifySSL'],
            $connectionConfig['timeout']
        );

        if ($connectionConfig['udp']['enabled']) {
            $client->setDriver(new UDP($client->getHost(), $connectionConfig['udp']['port']));
        }

        return new InfluxDBConnection($client->selectDB($connectionConfig['dbname']));
    }
}

<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Repositories\EventRepository;

use InfluxDB\Point;
use Umbrellio\EventTracker\Connections\Connection;

class EventRepository
{
    protected Connection $connection;
    protected array $config;

    public function __construct(Connection $connection, array $config)
    {
        $this->connection = $connection;
        $this->config = $config;
    }

    public function write(string $measurement, $val, array $tags = [], int $timestamp = null): void
    {
        if (!$this->config['enabled']) {
            return;
        }

        $measurementPrefix = $this->config['measurement_prefix'];
        $point = new Point("${measurementPrefix}.${measurement}", 1, $tags, compact('val'), $timestamp);

        $this->connection->write($point);
    }
}

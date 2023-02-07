<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Connections;

use InfluxDB\Database;
use InfluxDB\Point;

class InfluxDBConnection implements Connection
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function write(Point $point): void
    {
        $this->database->writePoints([$point]);
    }
}

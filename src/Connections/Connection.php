<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Connections;

use InfluxDB\Point;

interface Connection
{
    public function write(Point $point): void;
}

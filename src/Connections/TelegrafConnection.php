<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Connections;

use InfluxDB\Driver\UDP;
use InfluxDB\Point;

class TelegrafConnection implements Connection
{
    private $socket;

    public function __construct(UDP $udp)
    {
        $this->socket = $udp;
    }

    public function write(Point $point): void
    {
        $this->socket->write((string) $point);
    }
}

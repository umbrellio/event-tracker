<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Services;

class Timer
{
    private ?float $time;

    public function start(): void
    {
        $this->time = microtime(true);
    }

    public function end(): ?float
    {
        if ($this->time === null) {
            return null;
        }

        $result = microtime(true) - $this->time;

        $this->time = null;

        return $result;
    }
}

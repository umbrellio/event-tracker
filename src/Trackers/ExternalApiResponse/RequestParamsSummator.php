<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\ExternalApiResponse;

class RequestParamsSummator
{
    private $params = [];

    public function add(array $params): void
    {
        foreach ($params as $param => $value) {
            $this->params[$param] = ($this->params[$param] ?? 0) + $value;
        }
    }

    public function flush(): array
    {
        $params = $this->params;
        $this->params = [];

        return $params;
    }
}

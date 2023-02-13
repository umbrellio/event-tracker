<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\ResponseTime;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Umbrellio\EventTracker\Services\Adapters\BaseAdapter;

class ResponseTimeTrackerMiddleware
{
    private BaseAdapter $adapter;
    private array $metricConfig;

    public function __construct(BaseAdapter $adapter, array $metricConfig)
    {
        $this->adapter = $adapter;
        $this->metricConfig = $metricConfig;
    }

    public function handle($request, $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $responseTime = $this->calculateResponseTime();

        $this->adapter->write($this->metricConfig['measurement'], $responseTime, [
            'action' => $request->route()
                ->getActionName(),
        ]);
    }

    private function calculateResponseTime(): float
    {
        $laravelStart = defined('LARAVEL_START') ? LARAVEL_START : 0.0;

        return microtime(true) - $laravelStart;
    }
}

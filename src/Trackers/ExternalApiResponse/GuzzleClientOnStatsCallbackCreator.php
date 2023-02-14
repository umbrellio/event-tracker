<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\ExternalApiResponse;

use Closure;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;
use Umbrellio\EventTracker\Services\Adapters\BaseAdapter;
use Umbrellio\EventTracker\Services\Adapters\PrometheusHistogramAdapter;

class GuzzleClientOnStatsCallbackCreator
{
    private const DEFAULT_STATUS_CODE = 0;

    private BaseAdapter $adapter;
    private RequestParamsSummator $summator;
    private array $config;

    public function __construct(BaseAdapter $adapter, RequestParamsSummator $summator, array $config)
    {
        $this->adapter = $adapter;
        $this->summator = $summator;
        $this->config = $config;
    }

    public function create(): callable
    {
        return Closure::fromCallable([$this, 'saveStats']);
    }

    private function saveStats(TransferStats $stats): void
    {
        $metrics = $this->fetchMetrics($stats);

        if ($this->config['group_redirects_in_one_request'] ?? false) {
            $this->summator->add($metrics);

            if ($this->isRedirectResponse($stats->getResponse())) {
                return;
            }

            $metrics = $this->summator->flush();
        }

        $tags = [
            'host' => $stats->getRequest()
                ->getUri()
                ->getHost(),
            'status' => optional($stats->getResponse())
                ->getStatusCode() ?? self::DEFAULT_STATUS_CODE,
        ];

        if (!$this->adapter instanceof PrometheusHistogramAdapter) {
            $tags = array_merge($tags, $metrics);
        }

        $this->adapter->write($this->config['measurement'], $metrics[$this->config['main_metric']], $tags);
    }

    private function isRedirectResponse(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 300 && $response->getStatusCode() < 400;
    }

    private function fetchMetrics(TransferStats $stats): array
    {
        $params = [];

        foreach ($this->config['metrics'] as $metric) {
            $params[$metric] = $stats->getHandlerStat($metric);
        }

        return $params;
    }
}

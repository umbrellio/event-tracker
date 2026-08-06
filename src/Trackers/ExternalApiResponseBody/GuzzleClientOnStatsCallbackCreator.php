<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\ExternalApiResponseBody;

use Closure;
use GuzzleHttp\TransferStats;
use Umbrellio\EventTracker\Services\Adapters\BaseAdapter;

class GuzzleClientOnStatsCallbackCreator
{
    private const DEFAULT_STATUS_CODE = 0;

    private BaseAdapter $adapter;
    private MessageBodyFieldsExtractor $extractor;
    private array $config;

    public function __construct(BaseAdapter $adapter, MessageBodyFieldsExtractor $extractor, array $config)
    {
        $this->adapter = $adapter;
        $this->extractor = $extractor;
        $this->config = $config;
    }

    public function create(): callable
    {
        return Closure::fromCallable([$this, 'saveStats']);
    }

    private function saveStats(TransferStats $stats): void
    {
        $response = $stats->getResponse();

        $tags = [
            'host' => $stats->getRequest()
                ->getUri()
                ->getHost(),
            'status' => optional($response)
                    ->getStatusCode() ?? self::DEFAULT_STATUS_CODE,
        ];

        $tags = array_merge(
            $tags,
            $this->extractor->extract($stats->getRequest(), $this->config['request_fields'] ?? [], $this->config),
            $this->extractor->extract($response, $this->config['response_fields'] ?? [], $this->config)
        );

        $this->adapter->write($this->config['measurement'], 1, $tags);
    }
}

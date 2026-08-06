<?php

declare(strict_types=1);

namespace Tests\Feature\Trackers\ExternalApiResponseBody;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use PHPUnit\Framework\TestCase;
use Umbrellio\EventTracker\Services\Adapters\EventAdapter;
use Umbrellio\EventTracker\Trackers\ExternalApiResponseBody\GuzzleClientOnStatsCallbackCreator;
use Umbrellio\EventTracker\Trackers\ExternalApiResponseBody\MessageBodyFieldsExtractor;

class GuzzleClientOnStatsCallbackCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function writesMetricWithFieldsParsedFromRequestAndResponseBodies(): void
    {
        $eventAdapter = $this->createMock(EventAdapter::class);
        $eventAdapter->expects($this->once())
            ->method('write')
            ->with('external_api_response_body', 1, [
                'host' => 'domain.com',
                'status' => 200,
                'method' => 'pointg/sessions/create',
                'code' => 'incorrect_device_type',
            ]);

        $callback = $this->creator($eventAdapter)->create();

        $request = new Request(
            'POST',
            'https://domain.com/api/v2/service',
            [],
            json_encode(['method' => 'pointg/sessions/create'])
        );
        $response = new Response(200, [], json_encode(['error' => ['code' => 'incorrect_device_type']]));

        $callback(new TransferStats($request, $response));
    }

    /**
     * @test
     */
    public function usesDefaultValueWhenResponseHasNoError(): void
    {
        $eventAdapter = $this->createMock(EventAdapter::class);
        $eventAdapter->expects($this->once())
            ->method('write')
            ->with('external_api_response_body', 1, [
                'host' => 'domain.com',
                'status' => 200,
                'method' => 'pointg/sessions/create',
                'code' => 'unknown',
            ]);

        $callback = $this->creator($eventAdapter)->create();

        $request = new Request(
            'POST',
            'https://domain.com/api/v2/service',
            [],
            json_encode(['method' => 'pointg/sessions/create'])
        );
        $response = new Response(200, [], json_encode(['data' => ['url' => 'https://game.example']]));

        $callback(new TransferStats($request, $response));
    }

    /**
     * @test
     */
    public function usesDefaultStatusWhenRequestFailed(): void
    {
        $eventAdapter = $this->createMock(EventAdapter::class);
        $eventAdapter->expects($this->once())
            ->method('write')
            ->with('external_api_response_body', 1, [
                'host' => 'domain.com',
                'status' => 0,
                'method' => 'pointg/sessions/create',
                'code' => 'unknown',
            ]);

        $callback = $this->creator($eventAdapter)->create();

        $request = new Request(
            'POST',
            'https://domain.com/api/v2/service',
            [],
            json_encode(['method' => 'pointg/sessions/create'])
        );

        $callback(new TransferStats($request, null));
    }

    private function creator(EventAdapter $eventAdapter): GuzzleClientOnStatsCallbackCreator
    {
        return new GuzzleClientOnStatsCallbackCreator($eventAdapter, new MessageBodyFieldsExtractor(), [
            'measurement' => 'external_api_response_body',
            'request_fields' => ['method' => 'method'],
            'response_fields' => ['code' => 'error.code'],
        ]);
    }
}

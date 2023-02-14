<?php

declare(strict_types=1);

namespace Tests\Feature\Trackers\ExternalApiResponse;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Umbrellio\EventTracker\Services\Adapters\EventAdapter;
use Umbrellio\EventTracker\Trackers\ExternalApiResponse\GuzzleClientOnStatsCallbackCreator;
use Umbrellio\EventTracker\Trackers\ExternalApiResponse\RequestParamsSummator;

class GuzzleClientOnStatsCallbackCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function writeAllRequests(): void
    {
        $eventTracker = $this->mockEventAdapter(2);
        $summator = $this->mockSummator(0);

        $callback = $this->creator($eventTracker, $summator, false)
            ->create();

        $callback($this->transferStats(true));
        $callback($this->transferStats(false));
    }

    /**
     * @test
     */
    public function dontWriteEveryRequestIfRedirect(): void
    {
        $eventTracker = $this->mockEventAdapter(null);
        $eventTracker->expects($this->once())
            ->method('write')
            ->with('external_api_response', 2, [
                'host' => 'domain.com',
                'status' => 200,
                'total_time' => 2,
                'connect_time' => 2,
                'namelookup_time' => 2,
            ]);

        $callback = $this->creator($eventTracker, new RequestParamsSummator(), true)
            ->create();

        $callback($this->transferStats(true));
        $callback($this->transferStats(false));
    }

    private function mockEventAdapter(?int $expectsCallWrite): MockObject
    {
        $eventTracker = $this->createMock(EventAdapter::class);

        if ($expectsCallWrite) {
            $eventTracker->expects($this->exactly($expectsCallWrite))
                ->method('write');
        }

        return $eventTracker;
    }

    private function mockSummator(int $expectsCallAdd): MockObject
    {
        $summator = $this->createMock(RequestParamsSummator::class);
        $summator->expects($this->exactly($expectsCallAdd))
            ->method('add');

        return $summator;
    }

    private function transferStats(bool $isRedirect): TransferStats
    {
        $request = new Request('get', 'https://domain.com/1/edit');
        $response = new Response($isRedirect ? 301 : 200);

        return new TransferStats($request, $response, null, [], [
            'total_time' => 1,
            'connect_time' => 1,
            'namelookup_time' => 1,
        ]);
    }

    private function creator(
        MockObject $eventAdapter,
        $summator,
        bool $groupRedirectsInOneRequest
    ): GuzzleClientOnStatsCallbackCreator {
        return new GuzzleClientOnStatsCallbackCreator($eventAdapter, $summator, [
            'measurement' => 'external_api_response',
            'group_redirects_in_one_request' => $groupRedirectsInOneRequest,
            'metrics' => ['total_time', 'connect_time', 'namelookup_time'],
            'main_metric' => 'total_time',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Trackers\ResponseTime;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Umbrellio\EventTracker\Services\Adapters\EventAdapter;
use Umbrellio\EventTracker\Trackers\ResponseTime\ResponseTimeTrackerMiddleware;

class ResponseTimeTrackerMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function correctWrite(): void
    {
        define('LARAVEL_START', microtime(true));

        $measurement = 'test';
        $eventAdapter = $this->createMock(EventAdapter::class);
        $eventAdapter->expects($this->once())
            ->method('write')
            ->with($measurement, $this->greaterThan(0), [
                'action' => '',
            ]);

        $middleware = new ResponseTimeTrackerMiddleware($eventAdapter, compact('measurement'));
        $middleware->terminate(Request::create('domain.com/test'), new Response());
    }
}

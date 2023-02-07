<?php

declare(strict_types=1);

namespace Tests\Feature\Trackers\JobsDuration;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\JobCreator;
use Tests\Helpers\JobsFilterMockerTrait;
use Umbrellio\EventTracker\Services\Adapters\EventAdapter;
use Umbrellio\EventTracker\Services\Timer;
use Umbrellio\EventTracker\Trackers\JobsDuration\Listeners\EndTimerListener;
use Umbrellio\EventTracker\Trackers\JobsDuration\Listeners\StartTimerListener;

class JobsDurationTrackerTest extends TestCase
{
    use JobsFilterMockerTrait;

    /**
     * @test
     */
    public function dontLeaveOldTimeWhenJobSkipped(): void
    {
        $timer = new Timer();
        $eventAdapter = $this->mockEventAdapter();
        $eventAdapter->expects($this->once())
            ->method('write');

        $startListener = new StartTimerListener($timer, $this->mockJobsFilter([false, true]));
        $endListener = new EndTimerListener($timer, $eventAdapter, [
            'measurement' => 'test',
        ]);

        $job = JobCreator::createJob('test');

        $processingEvent = new JobProcessing('test', $job);
        $startListener->handle($processingEvent);
        $processedEvent = new JobProcessed('test', $job);
        $endListener->handle($processedEvent);

        $this->assertNull($timer->end());

        $processingEvent = new JobProcessing('test', $job);
        $startListener->handle($processingEvent);
        $processedEvent = new JobProcessed('test', $job);
        $endListener->handle($processedEvent);
    }

    private function mockEventAdapter(): MockObject
    {
        return $this->createMock(EventAdapter::class);
    }
}

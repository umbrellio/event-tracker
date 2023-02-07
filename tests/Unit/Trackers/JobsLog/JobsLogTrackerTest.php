<?php

declare(strict_types=1);

namespace Tests\Unit\Trackers\JobsLog;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\JobCreator;
use Tests\Helpers\JobsFilterMockerTrait;
use Umbrellio\EventTracker\Services\Adapters\EventAdapter;
use Umbrellio\EventTracker\Trackers\JobsLog\JobsLogTracker;

class JobsLogTrackerTest extends TestCase
{
    use JobsFilterMockerTrait;

    /**
     * @test
     */
    public function skipJobIfNeed(): void
    {
        $eventAdapter = $this->mockEventAdapter();
        $eventAdapter->expects($this->never())
            ->method('write');

        $filter = $this->mockJobsFilter();
        $tracker = new JobsLogTracker($eventAdapter, $filter, [
            'measurement' => 'test',
        ]);

        $tracker->write(JobCreator::createJob('test'), 'processing');
    }

    /**
     * @test
     */
    public function correctWrite(): void
    {
        $measurement = 'test';
        $jobName = 'test_job_name';

        $eventAdapter = $this->mockEventAdapter();
        $eventAdapter
            ->expects($this->once())
            ->method('write')
            ->with($measurement, '', [
                'eventName' => 'processing',
                'jobName' => $jobName,
            ]);

        $filter = $this->mockJobsFilter([false]);
        $tracker = new JobsLogTracker($eventAdapter, $filter, compact('measurement'));

        $tracker->write(JobCreator::createJob($jobName), 'processing');
    }

    private function mockEventAdapter(): MockObject
    {
        return $this->createMock(EventAdapter::class);
    }
}

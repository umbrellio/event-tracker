<?php

declare(strict_types=1);

namespace Tests\Helpers;

use PHPUnit\Framework\MockObject\MockObject;
use Umbrellio\EventTracker\Services\JobsFilter;

trait JobsFilterMockerTrait
{
    private function mockJobsFilter(array $stackResults = [true]): MockObject
    {
        $mock = $this->createMock(JobsFilter::class);
        $mock->method('needSkip')
            ->willReturnOnConsecutiveCalls(...$stackResults);

        return $mock;
    }
}

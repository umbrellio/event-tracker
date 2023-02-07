<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use Tests\Helpers\JobCreator;
use Umbrellio\EventTracker\Services\JobsFilter;

class JobsFilterTest extends TestCase
{
    /**
     * @test
     */
    public function skipJobFromConfig(): void
    {
        $skipJob = JobCreator::createJob('test');
        $notSkipJob = JobCreator::createJob('test2');
        $filter = new JobsFilter(['test']);

        $this->assertTrue($filter->needSkip($skipJob));
        $this->assertFalse($filter->needSkip($notSkipJob));
    }
}

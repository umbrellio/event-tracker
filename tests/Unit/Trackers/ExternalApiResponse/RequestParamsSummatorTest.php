<?php

declare(strict_types=1);

namespace Tests\Unit\Trackers\ExternalApiResponse;

use PHPUnit\Framework\TestCase;
use Umbrellio\EventTracker\Trackers\ExternalApiResponse\RequestParamsSummator;

class RequestParamsSummatorTest extends TestCase
{
    /**
     * @test
     */
    public function correctAdding(): void
    {
        $summator = new RequestParamsSummator();
        $summator->add([
            'param1' => 1,
            'param2' => 2,
        ]);
        $summator->add([
            'param1' => 10,
            'param2' => 20,
        ]);

        $params = $summator->flush();

        $this->assertSame($params['param1'], 11);
        $this->assertSame($params['param2'], 22);
        $this->assertEmpty($summator->flush());
    }
}

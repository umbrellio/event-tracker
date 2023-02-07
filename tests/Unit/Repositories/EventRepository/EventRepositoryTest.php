<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories\EventRepository;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Umbrellio\EventTracker\Connections\Connection;
use Umbrellio\EventTracker\Repositories\EventRepository\EventRepository;

class EventRepositoryTest extends TestCase
{
    private MockObject $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->createMock(Connection::class);
    }

    /**
     * @test
     */
    public function enabledConfig(): void
    {
        $this->connection->expects($this->never())
            ->method('write');
        $eventTracker = new EventRepository($this->connection, $this->createConfig(false));

        $eventTracker->write('test', 'test');
    }

    /**
     * @test
     */
    public function writeToConnection(): void
    {
        $this->connection->expects($this->once())
            ->method('write');
        $eventTracker = new EventRepository($this->connection, $this->createConfig(true));

        $eventTracker->write('test', 'test');
    }

    private function createConfig(bool $enabled): array
    {
        return [
            'enabled' => $enabled,
            'measurement_prefix' => 'test',
        ];
    }
}

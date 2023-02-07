<?php

declare(strict_types=1);

namespace Tests\Feature\Trackers\ExternalApiResponse;

use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;
use Umbrellio\EventTracker\Exceptions\IncorrectConfigException;
use Umbrellio\EventTracker\Trackers\ExternalApiResponse\Installer;

class InstallerTest extends TestCase
{
    /**
     * @test
     */
    public function exceptionIncorrectConfig(): void
    {
        $installer = new Installer();

        $this->expectException(IncorrectConfigException::class);
        $installer->install($this->createMock(Application::class), 'prometheus', [
            'metrics' => [],
            'main_metric' => 'metric',
        ]);
    }
}

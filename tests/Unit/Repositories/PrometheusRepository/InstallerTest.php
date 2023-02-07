<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories\PrometheusRepository;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\TestCase;
use Umbrellio\EventTracker\Repositories\PrometheusRepository\Installer;

class InstallerTest extends TestCase
{
    private Installer $installer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->installer = new Installer($this->createMock(Application::class));
    }

    /**
     * @test
     */
    public function exceptionIfEnabled(): void
    {
        $this->installer->install([
            'enabled' => false,
        ]);

        $this->expectException(Exception::class);

        $config = [
            'enabled' => true,
            'connections' => [
                'prometheus' => [
                    'metricsRoute' => [
                        'endpoint' => '/metrics',
                    ],
                ],
            ],
        ];

        $this->installer->install($config);
    }
}

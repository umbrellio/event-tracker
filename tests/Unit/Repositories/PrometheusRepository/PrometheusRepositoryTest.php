<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories\PrometheusRepository;

use PHPUnit\Framework\TestCase;
use Prometheus\CollectorRegistry;
use Umbrellio\EventTracker\Repositories\PrometheusRepository\PrometheusRepository;

class PrometheusRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function returnMetrics(): void
    {
        $mockRegistry = $this->createMock(CollectorRegistry::class);
        $mockRegistry->expects($this->once())
            ->method('getMetricFamilySamples');

        $repository = new PrometheusRepository($mockRegistry, []);
        $repository->getMetrics();
    }

    /**
     * @test
     */
    public function correctLabelsAndWriteCounter(): void
    {
        $mockRegistry = $this->createMock(CollectorRegistry::class);
        $mockRegistry->expects($this->once())
            ->method('getOrRegisterCounter')
            ->with('kek', 'metric', '', ['label1', 'label2']);

        $config = [
            'enabled' => true,
            'measurement_prefix' => 'kek',
            'connections' => [
                'prometheus' => [
                    'labels' => [
                        'label1' => 'value',
                    ],
                ],
            ],
        ];

        $repository = new PrometheusRepository($mockRegistry, $config);
        $repository->writeCounter('metric', [
            'label2' => 1,
        ]);
    }

    /**
     * @test
     */
    public function writeGauge(): void
    {
        $mockRegistry = $this->createMock(CollectorRegistry::class);
        $mockRegistry->expects($this->once())
            ->method('getOrRegisterGauge');

        $repository = new PrometheusRepository($mockRegistry, [
            'enabled' => true,
        ]);
        $repository->writeGauge('metric', 1);
    }

    /**
     * @test
     */
    public function writeHistogram(): void
    {
        $mockRegistry = $this->createMock(CollectorRegistry::class);
        $mockRegistry->expects($this->once())
            ->method('getOrRegisterHistogram');

        $repository = new PrometheusRepository($mockRegistry, [
            'enabled' => true,
        ]);
        $repository->writeHistogram('metric', 1);
    }

    /**
     * @test
     */
    public function writeSummary(): void
    {
        $mockRegistry = $this->createMock(CollectorRegistry::class);
        $mockRegistry->expects($this->once())
            ->method('getOrRegisterSummary');

        $repository = new PrometheusRepository($mockRegistry, [
            'enabled' => true,
        ]);
        $repository->writeSummary('metric', 1);
    }
}

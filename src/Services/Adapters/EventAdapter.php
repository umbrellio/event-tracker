<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Services\Adapters;

use Umbrellio\EventTracker\Repositories\EventRepository\EventRepository;

class EventAdapter extends BaseAdapter
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository, array $metricConfig)
    {
        $this->eventRepository = $eventRepository;

        parent::__construct($metricConfig);
    }

    public function write(string $measurement, $value = null, array $tags = []): void
    {
        $this->eventRepository->write($measurement, $value, $tags);
    }
}

<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Services;

use Illuminate\Support\Facades\Event;

class EventSubscriber
{
    public function subscribe(array $eventListenersMap): void
    {
        foreach ($eventListenersMap as $event => $listener) {
            Event::listen($event, $listener);
        }
    }
}

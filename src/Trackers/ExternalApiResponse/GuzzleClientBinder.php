<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\ExternalApiResponse;

use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;

class GuzzleClientBinder
{
    public function bind(Application $app): void
    {
        $app->bind(Client::class, function () use ($app) {
            $callbackCreator = $app->make(GuzzleClientOnStatsCallbackCreator::class);

            return new Client([
                'on_stats' => $callbackCreator->create(),
            ]);
        });
    }
}

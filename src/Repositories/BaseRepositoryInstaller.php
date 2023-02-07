<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Repositories;

use Illuminate\Contracts\Foundation\Application;

abstract class BaseRepositoryInstaller
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    abstract public function install(array $config): void;
}

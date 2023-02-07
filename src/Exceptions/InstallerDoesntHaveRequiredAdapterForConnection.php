<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Exceptions;

class InstallerDoesntHaveRequiredAdapterForConnection extends BaseException
{
    public function __construct(string $connection)
    {
        parent::__construct($connection);
    }
}

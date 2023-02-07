<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;

class JobCreator
{
    public static function createJob(string $name): Job
    {
        return new class($name) extends Job implements JobContract {
            private $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }

            public function attempts()
            {
            }

            public function getRawBody()
            {
                return json_encode([
                    'job' => $this->name,
                ]);
            }

            public function getJobId()
            {
            }
        };
    }
}

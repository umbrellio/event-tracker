<?php

declare(strict_types=1);

return [
    'enabled' => true,

    // all metric names will start with this prefix
    'measurement_prefix' => 'app',
    'connection' => 'telegraf',
    'connections' => [
        'telegraf' => [
            'host' => env('EVENT_TRACKER_TELEGRAF_HOST', 'localhost'),
            'port' => env('EVENT_TRACKER_TELEGRAF_PORT', 8094),
        ],
        'influxdb' => [
            'host' => env('EVENT_TRACKER_INFLUXDB_HOST', 'localhost'),
            'port' => env('EVENT_TRACKER_INFLUXDB_PORT', 8086),
            'username' => env('EVENT_TRACKER_INFLUXDB_USERNAME', ''),
            'password' => env('EVENT_TRACKER_INFLUXDB_PASSWORD', ''),
            'ssl' => env('EVENT_TRACKER_INFLUXDB_SSL', false),
            'verifySSL' => env('EVENT_TRACKER_INFLUXDB_VERIFYSSL', false),
            'timeout' => env('EVENT_TRACKER_INFLUXDB_TIMEOUT', 0),
            'dbname' => env('EVENT_TRACKER_INFLUXDB_DBNAME', ''),
            'udp' => [
                'enabled' => env('EVENT_TRACKER_INFLUXDB_UDP_ENABLED', false),
                'port' => env('EVENT_TRACKER_INFLUXDB_UDP_PORT', 8086),
            ],
        ],
        'prometheus' => [
            'redis' => [
                'client' => env('EVENT_TRACKER_REDIS_CLIENT', 'phpredis'),
                'connection' => [
                    'host' => env('EVENT_TRACKER_REDIS_HOST', 'localhost'),
                    'username' => env('EVENT_TRACKER_REDIS_USERNAME', 'redis'),
                    'password' => env('EVENT_TRACKER_REDIS_PASSWORD'),
                    'port' => env('EVENT_TRACKER_REDIS_PORT', '6379'),
                    'database' => env('EVENT_TRACKER_REDIS_DATABASE', 0),
                ]
            ],
            'labels' => [
                'namespace' => 'app_ns',
            ],
            'metricsRoute' => [
                'endpoint' => '/metrics',
            ],
        ],
    ],
    'trackers' => [
        'jobs_duration' => [
            'measurement' => 'event_tracker_horizon_jobs_duration',

            // job's classnames which must be skipped
            'skip_jobs' => [],
            'prom_buckets' => [0.5, 1, 5, 10, 30, 60, 120, 180, 240, 300, 600],
        ],
        'jobs_log' => [
            'measurement' => 'event_tracker_horizon_jobs_log',

            // job's classnames which must be skipped
            'skip_jobs' => [],
        ],
        'response_time' => [
            'measurement' => 'event_tracker_response_time',
            'prom_buckets' => [0.05, 0.1, 0.25, 0.5, 1, 2.5, 5, 10],
        ],
        'external_api_response' => [
            'measurement' => 'event_tracker_external_api_response',
            'group_redirects_in_one_request' => false,
            'metrics' => ['total_time', 'connect_time', 'namelookup_time'],
            'main_metric' => 'total_time',

            'prom_buckets' => [0.05, 0.1, 0.25, 0.5, 1, 2.5, 5, 10],
        ],
    ],
];

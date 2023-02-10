<?php

declare(strict_types=1);

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\Redis as RedisAdapter;

require __DIR__ . '/../vendor/autoload.php';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri === '/healthcheck') {
    echo 'ok';

    return;
}

if ($uri !== '/metrics') {
    http_response_code(404);

    return;
}

$config = [
    'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
    'port' => getenv('REDIS_PORT') ?: 6379,
    'password' => getenv('REDIS_PASSWORD') ?: null,
    'database' => getenv('REDIS_DATABASE') ?: 0,
];

$adapter = new RedisAdapter($config);
$registry = new CollectorRegistry($adapter, false);
$format = new RenderTextFormat();
$metrics = $registry->getMetricFamilySamples();
$rendered = $format->render($metrics);

ob_start('ob_gzhandler');

header('Content-Type: ' . RenderTextFormat::MIME_TYPE);
echo $rendered;

ob_end_flush();

<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Repositories\PrometheusRepository;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Prometheus\RenderTextFormat;

class PrometheusController extends Controller
{
    public function __construct()
    {
        $this->middleware(Authenticate::class);
    }

    public function metrics(PrometheusRepositoryContract $repository, RenderTextFormat $format): Response
    {
        $metrics = $format->render($repository->getMetrics());

        return response($metrics, 200, [
            'Content-Type' => RenderTextFormat::MIME_TYPE,
        ]);
    }
}

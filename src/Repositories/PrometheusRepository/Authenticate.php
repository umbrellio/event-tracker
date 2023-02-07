<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Repositories\PrometheusRepository;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Authenticate
{
    private static ?Closure $auth = null;

    public function handle(Request $request, callable $next): ?Response
    {
        if (self::$auth === null) {
            return $next($request);
        }

        return (self::$auth)($request) ? $next($request) : abort(403);
    }

    public static function setAuth(Closure $auth): void
    {
        self::$auth = $auth;
    }
}

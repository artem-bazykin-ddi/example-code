<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Sentry;

class SentryApiLoggerMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function terminate(Request $request, $response): void
    {
        Sentry::captureMessage(json_encode([
            'request' => [
                'parameters' => $request->all(),
                'headers' => $request->headers
            ],
            'response' => [
                'headers' => $response->headers,
                'body' => $response->getContent()
            ]
        ], JSON_THROW_ON_ERROR, 512), Sentry\Severity::info());
    }
}

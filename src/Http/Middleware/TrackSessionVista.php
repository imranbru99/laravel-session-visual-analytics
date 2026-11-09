<?php

namespace Imranbru99\SessionVisualAnalytics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Imranbru99\SessionVisualAnalytics\Services\SessionAnalyticsService;
use Symfony\Component\HttpFoundation\Response;

class TrackSessionVista
{
    public function __construct(
        protected SessionAnalyticsService $analytics
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->hasSession() && $request->isMethodSafe()) {
            try {
                $this->analytics->track($request);
            } catch (\Throwable) {
                // Never break the host application because of analytics.
            }
        }

        return $response;
    }
}

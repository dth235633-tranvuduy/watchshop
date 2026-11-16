<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            $method = $request->method();
            $action = match ($method) {
                'GET', 'HEAD' => 'view',
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => strtolower($method),
            };

            ActivityLog::record($request->user(), $action, sprintf('%s %s', $method, $request->path()), $request->route()?->getName(), $method, $request->fullUrl(), $request->ip(), $request->userAgent());
        }

        return $response;
    }
}

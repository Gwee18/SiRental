<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (! auth('web')->check()) {
            if ($request->expectsJson()) {
                abort(401, 'Unauthenticated.');
            }

            return redirect()->guest(
                route('login')
            );
        }

        return $next($request);
    }
}

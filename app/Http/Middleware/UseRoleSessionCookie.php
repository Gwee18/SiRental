<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UseRoleSessionCookie
{
    private const ADMIN_COOKIE = 'sirental_admin_session';

    private const CUSTOMER_COOKIE = 'sirental_customer_session';

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $cookieName = $request->is('admin')
            || $request->is('admin/*')
                ? self::ADMIN_COOKIE
                : self::CUSTOMER_COOKIE;

        config([
            'session.cookie' => $cookieName,
        ]);

        return $next($request);
    }
}

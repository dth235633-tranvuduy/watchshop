<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Restrict a route to a given user role.
     *
     * - Guest          -> redirect to the login page
     * - Matching role  -> allow through
     * - role=user      -> redirect to the customer home
     * - Any other role -> redirect to the storefront home
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('user.dangnhap'))
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        if ($user->role === $role) {
            return $next($request);
        }

        if ($user->role === 'user') {
            return redirect()->route('user.home')
                ->with('error', 'Bạn không có quyền truy cập khu vực quản trị.');
        }

        return redirect()->route('frontend.home')
            ->with('error', 'Bạn không có quyền truy cập khu vực này.');
    }
}
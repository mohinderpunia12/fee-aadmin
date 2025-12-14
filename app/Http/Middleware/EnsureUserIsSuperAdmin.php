<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();
        
        // Allow Livewire requests (Filament uses Livewire for forms)
        if (str_contains($path, 'livewire') || $request->header('X-Livewire')) {
            return $next($request);
        }

        // Allow all authentication-related routes (login, logout, password reset, etc.)
        $authRoutes = ['login', 'logout', 'password.request', 'password.reset', 'password.email'];
        
        foreach ($authRoutes as $route) {
            if (str_contains($path, $route)) {
                return $next($request);
            }
        }

        // Allow access if user is not authenticated (let Filament handle login redirect)
        if (!$request->user()) {
            return $next($request);
        }

        // Only check super admin status for authenticated users
        if (!$request->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super admin access required.');
        }

        return $next($request);
    }
}

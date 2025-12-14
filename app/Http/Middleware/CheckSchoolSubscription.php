<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSchoolSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Superusers can always access
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        $tenant = \Filament\Facades\Filament::getTenant();

        if ($tenant && !$tenant->hasActiveSubscription()) {
            // Allow access to subscription-related pages and API routes
            if ($request->routeIs('api.*') || 
                $request->routeIs('filament.*.pages.subscription*') ||
                str_contains($request->path(), 'subscription')) {
                return $next($request);
            }

            // Show warning but allow access (can be changed to redirect if needed)
            if (!$request->session()->has('subscription_warning_shown')) {
                session()->flash('subscription_expired', true);
                session()->flash('subscription_warning_shown', true);
            }

            // Optionally redirect to subscription page
            // return redirect()->route('filament.app.pages.subscription');
        }

        return $next($request);
    }
}

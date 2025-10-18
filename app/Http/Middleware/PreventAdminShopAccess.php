<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminShopAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has admin role
        if (auth()->check() && auth()->user()->role === 'admin') {
            // Redirect admin to admin dashboard with message
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Admin tidak dapat mengakses area customer. Anda telah dialihkan ke dashboard admin.');
        }

        return $next($request);
    }
}

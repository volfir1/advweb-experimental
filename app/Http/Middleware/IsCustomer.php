<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be authenticated to access this page');
        }

        // Check if the authenticated user is not an admin
        if (Auth::check() && !Auth::user()->is_admin) {
            return $next($request);
        }

        // If the user is an admin, redirect to a 403 error page
        return redirect('/403')->with('error', 'You do not have access to this page.');
    }
}

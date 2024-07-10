<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be authenticated to access this page');
        }

        if (!Auth::user()->is_admin) {
            return redirect()->route('login')->with('error', 'You do not have permission to access this page');
        }

        return $next($request);
    }
    }
    
    


<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::check()) {
            if (Auth::user()->is_admin) {
                return redirect('/admin/dashboard');
            } elseif (Auth::user()->is_customer) {
                return redirect('/customer/dashboard');
            } else {
                return redirect('/');
            }
        }

        return $next($request);
    }
}

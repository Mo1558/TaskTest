<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && auth()->user()->hasRole('Admin')) {
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}
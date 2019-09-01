<?php

namespace TravelCompanion\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return $request->expectsJSON()
                    ? response()->json([
                        "success" => false,
                        "message" => "You cannot be logged in to make this request.",
                    ], 403)
                    : redirect()->route('app');
        }

        return $next($request);
    }
}

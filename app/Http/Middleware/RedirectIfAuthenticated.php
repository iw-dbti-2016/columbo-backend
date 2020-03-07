<?php

namespace Columbo\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Columbo\Traits\APIResponses;

class RedirectIfAuthenticated
{
	use APIResponses;
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
                    ? $this->unauthorizedResponse("You cannot be logged in to make this request.")
                    : redirect()->route('app');
        }

        return $next($request);
    }
}

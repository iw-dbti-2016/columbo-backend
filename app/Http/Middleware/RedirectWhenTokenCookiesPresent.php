<?php

namespace Columbo\Http\Middleware;

use Closure;

class RedirectWhenTokenCookiesPresent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->cookie(config('api.jwt_sign_cookie_name')) &&
            $request->cookie(config('api.jwt_payload_cookie_name'))) {
            return redirect()->route('app');
        }

        return $next($request);
    }
}

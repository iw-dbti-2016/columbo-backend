<?php

namespace TravelCompanion\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Hash;

class ConvertCookiesToAuthorizationHeader
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
        $signCookie = $request->cookie(config('api.jwt_sign_cookie_name'));
        $payloadCookie = $request->cookie(config('api.jwt_payload_cookie_name'));

        if ($signCookie && $payloadCookie) {
            $request->headers->set('Authorization', 'bearer ' . $payloadCookie . '.' . $signCookie);
            $request->headers->set('web', Hash::make("true"));
        }

        return $next($request);
    }
}

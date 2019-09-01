<?php

namespace TravelCompanion\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! $request->user()) {
            return $request->expectsJson()
                    ? response()->json([
                        "success" => false,
                        "message" => "Unauthenticated.",
                    ], 401)
                    : Redirect::route('login');
        }

        if ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail()) {
            return $request->expectsJson()
                    ? response()->json([
                        "success" => false,
                        "message" => "Your email address is not verified.",
                    ], 403)
                    : Redirect::route($redirectToRoute ?: 'verification.notice');
        }

        return $next($request);
    }
}

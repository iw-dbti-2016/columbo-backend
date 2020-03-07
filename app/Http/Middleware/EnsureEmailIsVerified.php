<?php

namespace Columbo\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;
use Columbo\Traits\APIResponses;

class EnsureEmailIsVerified
{
	use APIResponses;
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
                    ? $this->unauthenticatedResponse()
                    : Redirect::route('login');
        }

        if ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail()) {
            return $request->expectsJson()
                    ? $this->unauthorizedResponse("Your email address is not verified.")
                    : Redirect::route($redirectToRoute ?: 'verification.notice');
        }

        return $next($request);
    }
}

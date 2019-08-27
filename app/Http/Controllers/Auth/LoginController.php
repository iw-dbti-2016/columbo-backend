<?php

namespace TravelCompanion\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use TravelCompanion\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/app';

    /**
     * Where to redirect users after logout.
     *
     * @var string
     */
    protected $redirectBack = '/auth/login';

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Create cookies
        // $token = explode(".", JWTAuth::fromUser($user));

        // $signCookie = Cookie::make(config('api.jwt_sign_cookie_name'), $token[2], config('jwt.ttl'), $path=null, $domain=null, $secure=false, $httpOnly=true, $raw=false, $sameSite='strict');
        // $payloadCookie = Cookie::make(config('api.jwt_payload_cookie_name'), $token[0] . '.' . $token[1], 0, $path=null, $domain=null, $secure=false, $httpOnly=false, $raw=false, $sameSite='strict');

        // return redirect($this->redirectTo)
        //                 ->cookie($signCookie)
        //                 ->cookie($payloadCookie);
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        $signCookie = Cookie::forget(config('api.jwt_sign_cookie_name'));
        $payloadCookie = Cookie::forget(config('api.jwt_payload_cookie_name'));

        return redirect($this->redirectBack)
                        ->cookie($signCookie)
                        ->cookie($payloadCookie);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['guest', 'withoutTokenCookies'])->except('logout');
    }
}

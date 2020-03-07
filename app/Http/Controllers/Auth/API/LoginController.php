<?php

namespace Columbo\Http\Controllers\Auth\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Columbo\Http\Controllers\Controller;
use Columbo\Http\Resources\User as UserResource;
use Columbo\Traits\APIResponses;
use Columbo\Traits\Auth\AuthenticatesUsersWithToken;

class LoginController extends Controller
{
    use AuthenticatesUsersWithToken, APIResponses;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function __construct()
    {
    	$this->middleware('auth:api')->except('login');
    	$this->middleware('guest:api')->only('login');
    }

    protected function authenticated(Request $request, $user)
    {
        return (new UserResource($user))
        			->additional([
        				"meta" => [
				            "token" => $this->token,
				            "token_type" => 'bearer',
				            "expires_in" => auth()->factory()->getTTL() * 60,
				        ],
				    ]);
    }

    protected function validationFailed(\Illuminate\Validation\Validator $validator)
    {
        return $this->validationFailedResponse($validator);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return $this->unauthenticatedResponse("Credentials do not match our records.");
    }

    protected function loggedOut(Request $request)
    {
    	$signCookie = Cookie::forget(config("api.jwt_payload_cookie_name"));
		$payloadCookie = Cookie::forget(config("api.jwt_sign_cookie_name"));

    	return $this->okResponse("Logged out successfully")
            		->cookie($signCookie)
            		->cookie($payloadCookie);
    }

    private function constructResponse(Request $request, $responseData, $responseCode)
    {
    	$token = explode(".", $responseData["token"]);

		$signCookie = Cookie::forget('jwt_sign');
		$payloadCookie = Cookie::forget('jwt_payload');

    	return $this->okResponse(null, $responseData, $responseCode)
					->cookie($signCookie)
					->cookie($payloadCookie);
    }
}

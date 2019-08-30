<?php

namespace TravelCompanion\Http\Controllers\Auth\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use TravelCompanion\Http\Controllers\Controller;
use TravelCompanion\Traits\Auth\AuthenticatesUsersWithToken;

class LoginController extends Controller
{
    use AuthenticatesUsersWithToken;

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
        if (! auth()->user()->email_verified_at) {
            return response()->json([
                "success" => false,
                "message" => "E-mail not verified",
            ], 401);
        }

        return $this->constructResponse($request, [
            "success" => true,
            "data" => [
                "token" => $this->token,
                "token_type" => 'bearer',
                "expires_in" => auth()->factory()->getTTL() * 60,
                "user" => auth()->user(),
            ]
        ], 200);
    }

    protected function validationFailed(\Illuminate\Validation\Validator $validator)
    {
        return response()->json([
            "success" => false,
            "message" => "Validation Failed",
            "errors" => $validator->errors()->jsonSerialize(),
        ], 422);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return response()->json([
            "success" => false,
            "message" => "Credentials do not match with our records",
        ], 401);
    }


    protected function loggedOut(Request $request)
    {
    	$signCookie = Cookie::forget(config("api.jwt_payload_cookie_name"));
		$payloadCookie = Cookie::forget(config("api.jwt_sign_cookie_name"));

    	return response()->json([
    		"success" => true,
    		"message" => "Logged out successfully",
    		"data" => [],
    	], 200)
    		->cookie($signCookie)
    		->cookie($payloadCookie);
    }

    private function constructResponse(Request $request, $responseData, $responseCode)
    {
    	$token = explode(".", $responseData["data"]["token"]);

    	if ($this->isBrowserRequest($request)) {
	    	$responseData["data"] = array_merge($responseData["data"], ["token" => "/"]);

			$signCookie = Cookie::make('jwt_sign', $token[2], 0, $path=null, $domain=null, $secure=false, $httpOnly=true, $raw=false, $sameSite='strict');
			$payloadCookie = Cookie::make('jwt_payload', $token[0] . '.' . $token[1], 0, $path=null, $domain=null, $secure=false, $httpOnly=false, $raw=false, $sameSite='strict');
		} else {
			$signCookie = Cookie::forget('jwt_sign');
			$payloadCookie = Cookie::forget('jwt_payload');
		}

    	return response()->json($responseData, $responseCode)
						->cookie($signCookie)
						->cookie($payloadCookie);
    }

    private function isBrowserRequest(Request $request)
    {
    	return Hash::check("true", $request->header('web'));
    }
}

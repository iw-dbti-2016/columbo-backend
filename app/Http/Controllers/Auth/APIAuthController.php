<?php

namespace TravelCompanion\Http\Controllers\Auth;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Http\Controllers\Controller;
use TravelCompanion\Traits\Auth\AuthenticatesUsersWithToken;
use TravelCompanion\Traits\Auth\RegistersUsersWithToken;
use TravelCompanion\User;

class APIAuthController extends Controller
{
    function __construct()
    {
        $this->middleware('auth:api');
    }

    public function refresh(Request $request)
    {
    	$token = auth()->refresh();

    	return $this->constructResponse($request, [
    		"success" => true,
    		"data" => [
				"token" => $token,
				"token_type" => 'bearer',
				"expires_in" => auth()->factory()->getTTL() * 60,
				"user" => auth()->user(),
			]
    	], 200);
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

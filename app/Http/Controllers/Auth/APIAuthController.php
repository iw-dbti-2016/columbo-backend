<?php

namespace TravelCompanion\Http\Controllers\Auth;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Http\Controllers\Controller;
use TravelCompanion\User;

class APIAuthController extends Controller
{
	function __construct()
	{
		$this->middleware('auth:api')->except(['register', 'login']);
	}

	public function register(Request $request)
	{
		$validator = Validator::make($request->all(), [
			"first_name" => "required|min:2|max:50|regex:/^[A-Za-z-']+$/",
            "middle_name" => ["nullable", "max:100", "regex:/^((([A-Z]{1}\.)|([A-Za-z-']+)) ?)+$/"],
            "last_name" => "required|min:2|max:50|regex:/^[A-Za-z-']+$/",
            "username" => "required|min:4|max:40|regex:/^[A-Za-z0-9-.]+$/|unique:users",
            "email" => "required|max:80|email|unique:users",
            "home_location" => ["required", "regex:/^(-?([0-8][0-9]?\.[0-9]{1,8}|90\.[0]{1,8}) -?([0-9]{1,2}\.[0-9]{1,8}|1[0-7][0-9]\.[0-9]{1,8}|180\.[0]{1,8}))$/"],
            "password" => "required|min:6|confirmed",
		]);

		if (! $validator->passes()) {
			return response()->json([
				"success" => false,
				"message" => "Validation Failed",
				"errors" => $validator->errors()->jsonSerialize(),
			], 422);
		}

		$request["home_location"] = Point::fromString($request->home_location);
		$request["password"] = Hash::make($request->password);

		User::create($request->all());

		return response()->json([
			"success" => true,
			"data" => $request->all(),
		], 201);
	}

    public function login(Request $request)
    {
    	$validator = Validator::make($request->all(), [
			'email' => 'required',
			'password' => 'required',
		]);

		if (! $validator->passes()) {
			return response()->json([
				"success" => false,
				"message" => "Validation Failed",
				"errors" => $validator->errors()->jsonSerialize(),
			], 422);
		}

    	if (! $token = auth()->attempt(request(['email', 'password'])))
    	{
    		return response()->json([
    			"success" => false,
    			"message" => "Credentials do not match with our records",
    		], 401);
    	}

    	if (! $request->user()->email_verified_at) {
    		return response()->json([
    			"success" => false,
    			"message" => "E-mail not verified",
    		], 401);
    	}

    	return $this->constructResponse($request, [
    		"success" => true,
    		"data" => [
				"token" => $token,
				"token_type" => 'bearer',
				"expires_in" => auth()->factory()->getTTL() * 60,
				"user" => $request->user(),
			]
    	], 200);
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
				"user" => $request->user(),
			]
    	], 200);
    }

    public function logout(Request $request)
    {
    	auth()->logout();

		$signCookie = Cookie::forget('jwt_sign');
		$payloadCookie = Cookie::forget('jwt_payload');

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
    	return Hash::check("true", $request->cookie('browser'));
    }
}

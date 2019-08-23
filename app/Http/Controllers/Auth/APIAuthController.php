<?php

namespace TravelCompanion\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Http\Controllers\Controller;
use TravelCompanion\User;

class APIAuthController extends Controller
{
	public function register(Request $request)
	{
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

    	return response()->json([
    		"success" => true,
    		"data" => [
				"token" => $token,
				"token_type" => 'bearer',
				"expires_in" => auth()->factory()->getTTL() * 60,
				"user" => $request->user(),
			]
    	], 200);
    }
}

<?php

namespace Columbo\Http\Controllers\Auth;

use Columbo\Http\Controllers\Controller;
use Columbo\Http\Resources\User;
use Columbo\Traits\APIResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TokenController extends Controller
{
	use APIResponses;

	function __construct()
	{
		$this->middleware('auth:sanctum');
	}

	public function refresh(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'device_name'     => 'required',
		]);

		if ($validator->fails()) {
			return $this->validationFailedResponse($validator);
		}

		// Remove the current access token.
		$request->user()->currentAccessToken()->delete();

		// Create new token for device.
		$token = $request->user()->createToken($request->device_name)->plainTextToken;

		return (new User($request->user(), $token))
					->response()
					->setStatusCode(200);
	}
}

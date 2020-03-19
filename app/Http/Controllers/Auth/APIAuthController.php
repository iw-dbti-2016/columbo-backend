<?php

namespace Columbo\Http\Controllers\Auth;

use Columbo\Http\Controllers\Controller;
use Columbo\Http\Resources\User as UserResource;
use Columbo\Traits\APIResponses;
use Columbo\Traits\Auth\AuthenticatesUsersWithToken;
use Columbo\Traits\Auth\RegistersUsersWithToken;
use Columbo\User;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class APIAuthController extends Controller
{
	use APIResponses;

	function __construct()
	{
		$this->middleware('auth:airlock');
	}

	public function refresh(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'device_name'     => 'required',
		]);

		if ($validator->fails()) {
			return $this->validationFailedResponse($validator);
		}

		$tokens = $request->user()->tokens()->where('name', $request->device_name);

		if ($tokens->count() == 0) {
			return $this->unauthorizedResponse("No token in existence for this device.");
		}

		$tokens->delete();

		$token = $request->user()->createToken($request->device_name)->plainTextToken;

		return (new UserResource($request->user(), $token))
					->response()
					->setStatusCode(200);
	}
}

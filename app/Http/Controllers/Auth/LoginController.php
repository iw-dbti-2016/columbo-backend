<?php

namespace Columbo\Http\Controllers\Auth;

use Columbo\Http\Controllers\Controller;
use Columbo\Http\Resources\User as UserResource;
use Columbo\Traits\APIResponses;
use Columbo\Traits\Auth\AuthenticatesUsersWithToken;
use Columbo\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
		$this->middleware('auth:airlock')->except('login');
		$this->middleware('guest:airlock')->only('login');
	}

	protected function attemptLogin(Request $request)
	{
		$user = User::where('email', $request->email)->first();

		if (!$user || !Hash::check($request->password, $user->password)) {
			return false;
		}

		$this->token = $user->createToken($request->device_name)->plainTextToken;
		return $user;
	}

	protected function authenticated(Request $request, $user)
	{
		return new UserResource($user, $this->token);
	}

	protected function validationFailed(\Illuminate\Validation\Validator $validator)
	{
		return $this->validationFailedResponse($validator);
	}

	protected function sendFailedLoginResponse(Request $request)
	{
		return $this->unauthenticatedResponse("Credentials do not match our records.");
	}

	public function logout(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'device_name'     => 'required',
		]);

		if ($validator->fails()) {
			return $this->validationFailedResponse($validator);
		}

		$request->user()->tokens()->where('name', $request->device_name)->delete();

		return $this->loggedOut($request) ?: redirect('/');
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

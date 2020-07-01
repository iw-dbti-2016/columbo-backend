<?php

namespace Columbo\Http\Controllers\Auth;

use Columbo\Http\Controllers\Controller;
use Columbo\Http\Resources\User as UserResource;
use Columbo\Traits\APIResponses;
use Columbo\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
	use ThrottlesLogins, APIResponses;

	private $token;

	function __construct()
	{
		$this->middleware('auth:sanctum')->except('login');
		$this->middleware('guest:sanctum')->only('login');
	}

	public function login(Request $request)
	{
		$validator = Validator::make($request->all(), [
			$this->username() => 'required|string|email',
			'remember'        => 'nullable|boolean',
			'password'        => 'required|string',
			'device_name'     => 'required',
		]);

		if ($validator->fails()) {
			return $this->validationFailedResponse($validator);
		}

		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		if (method_exists($this, 'hasTooManyLoginAttempts') &&
			$this->hasTooManyLoginAttempts($request)) {
			$this->fireLockoutEvent($request);

			return $this->unauthenticatedResponse("Too many wrong attempts, you can temporarily not login.");
		}

		$user = $this->attemptLogin($request);
		if ($user) {
			if ($request->attributes->get('sanctum') === true) {
				$remember_user = $request->has('remember') && $request->remember;

				Auth::login($user, $remember_user);
			} else {
				$this->token = $user->createToken($request->device_name)->plainTextToken;
			}

			return $this->sendLoginResponse($request, $user);
		}

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);

		return $this->unauthenticatedResponse("Credentials do not match our records.");
	}

	private function attemptLogin(Request $request)
	{
		$user = User::where('email', $request->email)->first();

		if (!$user || !Hash::check($request->password, $user->password)) {
			return false;
		}

		return $user;
	}

	private function sendLoginResponse(Request $request, $user)
	{
		$this->clearLoginAttempts($request);

		return new UserResource($user, $this->token);
	}

	protected function username()
	{
		return 'email';
	}
}

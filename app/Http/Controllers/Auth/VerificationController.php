<?php

namespace Columbo\Http\Controllers\Auth;

use Columbo\Http\Controllers\Controller;
use Columbo\Traits\APIResponses;
use Columbo\Traits\Auth\VerifiesEmailsWithToken;
use Columbo\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
	use RedirectsUsers, APIResponses;

	protected $redirectTo = '/';

	public function __construct()
	{
		$this->middleware('auth:sanctum')->except('verify');
		$this->middleware(['signed', 'guest:sanctum'])->only('verify');
		$this->middleware('throttle:6,1')->only('verify', 'resend');
	}

	public function verify(Request $request)
	{
		$user = User::find($request->route('id'));

		if (! $user) {
			return abort(403, "This link is invalid");
		}

		if ($user->hasVerifiedEmail()) {
			return $this->alreadyVerifiedResponse($request);
		}

		if ($user->markEmailAsVerified()) {
			event(new Verified($user));
		}

		return redirect($this->redirectPath())
				->with('message', 'Your email has now been verified. You can continue in the app.');
	}

	public function resend(Request $request)
	{
		if ($request->user()->hasVerifiedEmail()) {
			return $this->alreadyVerifiedResponse($request);
		}

		$request->user()->sendEmailVerificationNotification();

		return $this->okResponse("The email has been re-sent.");
	}

	private function alreadyVerifiedResponse(Request $request)
	{
		if ($request->expectsJSON()) {
			return $this->okResponse("Your email is already verified.");
		} else {
			return redirect($this->redirectPath())
					->with('message', 'Your email is already verified. You can continue in the app.');
		}
	}
}

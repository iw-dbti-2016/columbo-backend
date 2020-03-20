<?php

namespace Columbo\Http\Controllers\Auth;

use Columbo\Http\Controllers\Controller;
use Columbo\Traits\APIResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
	use APIResponses;

	function __construct()
	{
		$this->middleware('guest:sanctum');
	}

	public function sendResetLinkEmail(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
		]);

		if ($validator->fails()) {
			return $this->validationFailedResponse($validator);
		}

		$response = Password::broker()->sendResetLink(
			$request->only('email')
		);

		return $response == Password::RESET_LINK_SENT
					? $this->okResponse(trans($response))
					: $this->validationFailedManualResponse(["email" => trans($response)]);
	}
}

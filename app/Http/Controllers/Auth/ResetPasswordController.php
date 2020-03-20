<?php

namespace Columbo\Http\Controllers\Auth;

use Columbo\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
	use ResetsPasswords;

	protected $redirectTo = '/';

	public function __construct()
	{
		$this->middleware('guest:sanctum');
	}

	protected function sendResetResponse(Request $request, $response)
	{
		if ($request->wantsJson()) {
			return new JsonResponse(['message' => trans($response)], 200);
		}

		return redirect($this->redirectPath())
				->with('message', 'Your password has been reset. You can go to the app and log in.');
	}
}

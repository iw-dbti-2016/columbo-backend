<?php

namespace Columbo\Http\Controllers\Auth;

use Columbo\Http\Controllers\Controller;
use Columbo\Traits\APIResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogoutController extends Controller
{
	use APIResponses;

	function __construct()
	{
		$this->middleware('auth:sanctum');
	}

	public function logout(Request $request)
	{
		$request->user()->currentAccessToken()->delete();

		return $this->okResponse("Logged out successfully.");
	}
}

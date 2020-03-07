<?php

namespace Columbo\Http\Controllers\Auth;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Columbo\Http\Controllers\Controller;
use Columbo\Http\Resources\User as UserResource;
use Columbo\Traits\Auth\AuthenticatesUsersWithToken;
use Columbo\Traits\Auth\RegistersUsersWithToken;
use Columbo\User;

class APIAuthController extends Controller
{
	function __construct()
	{
		$this->middleware('auth:api');
	}

	public function refresh(Request $request)
	{
		$token = auth()->refresh();

		return $this->constructResponse($request,
										array_merge(
											(new UserResource(auth()->user()))
											->toArray($request),
											[
												"meta" => [
													"token"      => $token,
													"token_type" => 'bearer',
													"expires_in" => auth()->factory()->getTTL() * 60,
												],
											])
										, 200);
	}

	private function constructResponse(Request $request, $responseData, $responseCode)
	{
		$token = explode(".", $responseData["meta"]["token"]);

		if ($this->isBrowserRequest($request)) {
			$responseData["data"] = array_merge($responseData["meta"], ["token" => "/"]);

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
		return Hash::check("true", $request->header('web'));
	}
}

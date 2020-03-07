<?php

namespace Columbo\Http\Controllers\Auth\API;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Columbo\Http\Controllers\Controller;
use Columbo\Http\Resources\User as UserResource;
use Columbo\Traits\APIResponses;
use Columbo\Traits\Auth\RegistersUsersWithToken;
use Columbo\User;

class RegisterController extends Controller
{
	use RegistersUsersWithToken, APIResponses;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	function __construct()
	{
		$this->middleware('auth:api')->except('register');
		$this->middleware('guest:api')->only('login');
	}

	protected function validator(array $data)
	{
		return Validator::make($data, [
			"data.type"                   => "required|string|in:user",
			"data.attributes.first_name"  => "required|min:2|max:50|regex:/^[A-Za-z-']+$/",
			"data.attributes.middle_name" => ["nullable", "max:100", "regex:/^((([A-Z]{1}\.)|([A-Za-z-']+)) ?)+$/"],
			"data.attributes.last_name"   => "required|min:2|max:50|regex:/^[A-Za-z-']+$/",
			"data.attributes.username"    => "required|min:4|max:40|regex:/^[A-Za-z0-9-.]+$/|unique:users,username",
			"data.attributes.email"       => "required|max:80|email|unique:users,email",
			"data.attributes.password"    => "required|min:6|confirmed",
		]);
	}

	protected function validationFailed(\Illuminate\Validation\Validator $validator)
	{
		return $this->validationFailedResponse($validator);
	}

	protected function create(array $data)
	{
		$data = $data["data"]["attributes"];

		return User::create([
			'first_name'  => $data['first_name'],
			'middle_name' => isset($data['middle_name']) ? $data["middle_name"] : null,
			'last_name'   => $data['last_name'],
			'username'    => $data['username'],
			'email'       => $data['email'],
			'password'    => Hash::make($data['password']),
		]);
	}

	protected function registered(Request $request, $user)
	{
		return (new UserResource($user))
					->additional([
						"meta" => [
							"token" => $this->token,
							"token_type" => 'bearer',
							"expires_in" => auth()->factory()->getTTL() * 60,
						],
					])
					->response()
					->setStatusCode(201);
	}
}

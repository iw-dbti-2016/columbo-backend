<?php

namespace Columbo\Http\Controllers\Auth;

use Columbo\Http\Controllers\Controller;
use Columbo\Http\Resources\User as UserResource;
use Columbo\Traits\APIResponses;
use Columbo\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
	use APIResponses;

	private $token;

	function __construct()
	{
		$this->middleware('auth:airlock')->except('register');
		$this->middleware('guest:airlock')->only('register');
	}

	public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
			"first_name"  => "required|min:2|max:50|regex:/^[A-Za-z-']+$/",
			"middle_name" => ["nullable", "max:100", "regex:/^((([A-Z]{1}\.)|([A-Za-z-']+)) ?)+$/"],
			"last_name"   => "required|min:2|max:50|regex:/^[A-Za-z-']+$/",
			"username"    => "required|min:4|max:40|regex:/^[A-Za-z0-9-.]+$/|unique:users,username",
			"email"       => "required|max:80|email|unique:users,email",
			"password"    => "required|min:6|confirmed",
			"device_name" => "required",
		]);

        if ($validator->fails()) {
            return $this->validationFailedResponse($validator);
        }

        event(new Registered($user = $this->create($request->all())));

        $this->token = $user->createToken($request->device_name)->plainTextToken;

        return (new UserResource($user, $this->token))
					->response()
					->setStatusCode(201);
    }

	private function create(array $data)
	{
		return User::create([
			'first_name'  => $data['first_name'],
			'middle_name' => isset($data['middle_name']) ? $data["middle_name"] : null,
			'last_name'   => $data['last_name'],
			'username'    => $data['username'],
			'email'       => $data['email'],
			'password'    => Hash::make($data['password']),
		]);
	}
}

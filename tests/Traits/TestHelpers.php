<?php

namespace Tests\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Tymon\JWTAuth\Facades\JWTAuth;

trait TestHelpers
{
	private $uri = "/";

	protected function setRefererUri($uri)
	{
		$this->uri = $uri;
	}

	public function ref($uri=null)
	{
		return $this->followingRedirects()
					->withHeader('referer', 'http://127.0.0.1:8000' . ($uri ?: $this->uri));
	}

	public function callAsUser(Authenticatable $user, $method, $uri, $parameters=[])
	{
		$token = JWTAuth::fromUser($user);
		$token = explode(".", $token);

		$cookies = [
			config("api.jwt_payload_cookie_name") => $token[0] . '.' . $token[1],
			config("api.jwt_sign_cookie_name") => Crypt::encrypt($token[2], false),
		];

		return $this->call($method, $uri, $parameters, $cookies);
	}
}

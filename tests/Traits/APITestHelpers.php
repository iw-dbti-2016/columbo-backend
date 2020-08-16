<?php

namespace Tests\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Tests\Traits\TestHelpers;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

trait APITestHelpers
{
	use TestHelpers;

	/**
	 * Adds the Accept header to the request.
	 */
	public function expectJSON()
	{
		$this->withHeader('Accept', 'Application/json');

		return $this;
	}

	public function showResponse($response)
	{
		dd($response->decodeResponseJson());
	}

	/////////////////////////////////////
	// STRUCTURE AND STATUS ASSERTIONS //
	/////////////////////////////////////

	protected function userResourceStructure()
	{
		return [
			"email",
			"username",
			"first_name",
			"last_name",
		];
	}

	protected function successStructure()
	{
		return [
			"data" => [
				"type",
				"id",
				"attributes",
			],
			"links" => [
				"self",
			],
		];
	}

	protected function successCollectionStructure()
	{
		return [
			"data",
			"links",
		];
	}

	protected function errorStructure()
	{
		return [
			"errors",
		];
	}

	protected function assertUnauthenticated($response)
	{
		$response->assertStatus(401);
		$response->assertJSONStructure($this->errorStructure());
	}

	protected function assertUnauthorized($response)
	{
		$response->assertStatus(403);
		$response->assertJSONStructure($this->errorStructure());
	}

	protected function assertNotFound($response)
	{
		$response->assertStatus(404);
		$response->assertJSONStructure($this->errorStructure());
	}

	protected function assertValidationFailed($response)
	{
		$response->assertStatus(422);
		$response->assertJSONStructure($this->errorStructure());
	}
}

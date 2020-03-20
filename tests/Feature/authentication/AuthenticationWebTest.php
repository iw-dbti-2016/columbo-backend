<?php

namespace Tests\Feature;

use Columbo\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\TestHelpers;

class AuthenticationWebTest extends TestCase
{
	use RefreshDatabase, TestHelpers;


	//////////////////////////
	//    RESET PASSWORD    //
	//////////////////////////

	/** @test */
	public function an_authenticated_user_cannot_see_reset_password_form()
	{
		$this->withoutExceptionHandling();
		$user = $this->createUser();

		Sanctum::actingAs($user);
		$response = $this->get("/auth/password/reset/token");

		$response->assertRedirect("/");
	}

	/** @test */
	public function an_authenticated_user_cannot_reset_password()
	{
		$user = $this->createUser();

		Sanctum::actingAs($user);
		$response = $this->post("/auth/password/reset", [
						"token" => "abcdef",
						"email" => $user->email,
						"password" => "password2",
						"password_confirmation" => "password2",
					]);

		$response->assertRedirect("/");
		$this->assertFalse(Hash::check("password2", User::first()->password));
	}

	/** @test */
	public function unauthenticated_user_can_see_password_reset_form()
	{
		$response = $this->get("/auth/password/reset/token");

		$response->assertStatus(200);
	}

	/** @test */
	public function unauthenticated_user_cannot_reset_password_with_invalid_token()
	{
		$user = $this->createUser();

		$response = $this->post("/auth/password/reset", [
						"token" => "abc",
						"email" => $user->email,
						"password" => "password2",
						"password_confirmation" => "password2",
					]);

		$response->assertStatus(302);
		$response->assertSessionHasErrors();

		$this->assertFalse(Hash::check("password2", User::first()->password));
	}

	/** @test */
	public function unauthenticated_user_with_valid_token_can_reset_password()
	{
		$this->withoutExceptionHandling();
		$user = $this->createUser();

		$response = $this->post("/api/v1/auth/password/email", [
			'email' => $user->email,
		]);

		$response->assertStatus(200);

		$this->assertDatabaseHas('password_resets', [
			'email' => $user->email,
		]);

		DB::update('update password_resets set token = ? where email = ?', [Hash::make("abc"), $user->email]);

		$response = $this->post("/auth/password/reset", [
						"token" => "abc",
						"email" => $user->email,
						"password" => "password2",
						"password_confirmation" => "password2",
					]);

		$response->assertStatus(302);
		$response->assertSessionHas('message');

		$this->assertTrue(Hash::check("password2", User::where('email', $user->email)->first()->password));
	}


	//////////////////////////////
	//    EMAIL VERIFICATION    //
	//////////////////////////////

	/** @test */
	public function an_invalid_token_results_in_an_error()
	{
		$response = $this->get('/auth/email/verify/10000/token_that_is_invalid');

		$response->assertStatus(403);
	}

	// 404, actually does not belong in this file
	/** @test */
	public function any_request_to_inexisting_page_returns_a_404()
	{
		$response = $this->post("/404");

		$response->assertStatus(404);
	}

	protected function getTestAttributes()
	{
		return [
			"first_name"            => "John",
			"middle_name"           => "R.",
			"last_name"             => "Doe",
			"username"              => "johndoe",
			"email"                 => "john@example.com",
			"password"              => "password",
			"password_confirmation" => "password",
		];
	}

	protected function getResourceType()
	{
		return "user";
	}
}

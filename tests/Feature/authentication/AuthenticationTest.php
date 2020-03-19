<?php

namespace Tests\Feature;

use Columbo\Currency;
use Columbo\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Airlock\Airlock;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class AuthenticationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;


	////////////////////////
	//    REGISTRATION    //
	////////////////////////

	/** @test */
	public function a_user_can_register_with_basic_information()
	{
		$this->withoutExceptionHandling();
		$response = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestData());

		$response->assertStatus(201);
		$response->assertJSONStructure($this->userResourceStructure());

		$this->assertDatabaseHas("users", $this->getTestAttributesWithout(["password", "password_confirmation"]));
		$this->assertDatabaseHas("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}

	/** @test */
	public function a_user_cannot_register_without_all_required_fields()
	{
		$required_fields = ["first_name", "last_name", "username", "email", "password", "password_confirmation"];

		$responses = [];

		foreach ($required_fields as $field) {
			$responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWithout($field));
		}

		foreach ($responses as $i => $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());

			$this->assertDatabaseMissing("users", [
				"username" => "johndoe",
			]);

			$this->assertDatabaseMissing("users", [
				"email" => "john@example.com"
			]);

			$this->assertDatabaseMissing("personal_access_tokens", [
				"name" => "test-device-name",
			]);
		}
	}

	/** @test */
	public function a_user_cannot_register_with_wrong_data()
	{
		$wrong_data_fields = [
			// First name wrong characters [A-Za-z-']{2,50}
			["first_name" => "Johnnythebest!!"],
			// First name too long
			["first_name" => "JohnDoeTheBestWithAnExtremelyUnneccesaryLongNameWantsToRegisterForThisApplicationTooPleaseLetMeInIAmSoHypedRightNow"],
			// First name too short
			["first_name" => "J"],
			// Middle name wrong characters [A-Za-z-'. ]{0,100} (little more complicated regex: points only after word group etc)
			["middle_name" => "#R."],
			// Middle name too long
			["middle_name" => "Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert"],
			// Last name wrong characters [A-Za-z-']{2,50}
			["last_name" => "Doe45"],
			// Last name too short
			["last_name" => "D"],
			// Last name too long
			["last_name" => "DoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoe"],
			// Username wrong characters [A-Za-z0-9-.]{4,40}
			["username" => "johndoe9 :)"],
			// Username too short
			["username" => "jd"],
			// Username too long
			["username" => "johndoejohndoejohndoejohndoejohndoejohndoe11"],
			// Invalid email
			["email" => "john12example.com"],
			// Invalid email
			["email" => "john13@example@example.com"],
			// Email too long (max 80)
			["email" => "johnjohnjohnjohnjohnjohn14@examplexamplexamplexamplexamplexamplexample.comcomcomcom"],
			// Password too short .*{}
			[
				"password" => "pw",
				"password_confirmation" => "pw"
			],
			// Password confirmation not matching
			[
				"password" => "password",
				"password_confirmation" => "pass",
			]
		];

		$responses = [];

		foreach($wrong_data_fields as $field) {
			$responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith($field));
		}

		foreach ($responses as $i => $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("users", [
			"first_name" => "John",
			"last_name" => "Doe",
		]);
		$this->assertDatabaseMissing("users", [
			"username" => "johndoe",
		]);
		$this->assertDatabaseMissing("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}

	/** @test */
	public function additional_field_are_ignored_on_registration()
	{
		$response = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
			"birth_date" => "2019-01-01",
			"grandfather_last_name" => "Doedoe",
			"grandmother_favorite_color" => "yellow, but not yellow yellow, rather yellow-red-ish",
		]));

		$response->assertStatus(201);
		$response->assertJSONStructure($this->userResourceStructure());

		$this->assertDatabaseHas("users", [
			"username" => "johndoe",
		]);

		$this->assertDatabaseMissing("users", [
			"birth_date" => "2019-01-01",
		]);

		$this->assertDatabaseHas("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}

	/** @test */
	public function a_user_cannot_register_when_logged_in()
	{
		Airlock::actingAs($this->createUser());
		$response = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestData());

		$response->assertStatus(403);
		$response->assertJSONStructure($this->errorStructure());

		$this->assertDatabaseMissing("users", $this->getTestAttributesWithout(["password", "password_confirmation"]));
		$this->assertDatabaseMissing("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}


	/////////////////
	//    LOGIN    //
	/////////////////

	/** @test */
	public function a_user_can_log_in()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()->post("/api/v1/auth/login", [
			"email" => $user->email,
			"password" => "password",
			"device_name" => "test-device-name",
		]);

		$response->assertStatus(200);
		$response->assertJSONStructure($this->userResourceStructure());

		$this->assertDatabaseHas("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}

	/** @test */
	public function a_user_cannot_log_in_with_wrong_credentials()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()->post("/api/v1/auth/login", [
			"email" => $user->email,
			"password" => "pwd",
			"device_name" => "test-device-name",
		]);

		$response->assertStatus(401);
		$response->assertJSONStructure($this->errorStructure());

		$response = $this->expectJSON()->post("/api/v1/auth/login", [
			"email" => "abc@donttryme.com",
			"password" => "password",
			"device_name" => "test-device-name",
		]);

		$response->assertStatus(401);
		$response->assertJSONStructure($this->errorStructure());

		$this->assertDatabaseMissing("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}

	/** @test */
	public function a_user_cannot_login_without_email_and_password()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()->post("/api/v1/auth/login", [
			"password" => "password",
			"device_name" => "test-device-name",
		]);

		$response->assertStatus(422);
		$response->assertJSONStructure($this->errorStructure());

		$response = $this->expectJSON()->post("/api/v1/auth/login", [
			"email" => $user->email,
			"device_name" => "test-device-name",
		]);

		$response->assertStatus(422);
		$response->assertJSONStructure($this->errorStructure());

		$this->assertDatabaseMissing("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}

	/** @test */
	public function additional_fields_are_ignored_on_login()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()->post("/api/v1/auth/login", [
			"email" => $user->email,
			"password" => "password",
			"birth_date" => "2019-01-01",
			"additional_resource" => "Recipe for chocolate chip cookies :)",
			"device_name" => "test-device-name",
		]);

		$response->assertStatus(200);
		$response->assertJSONStructure($this->userResourceStructure());

		$this->assertDatabaseHas("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}

	/** @test */
	public function a_user_cannot_login_when_logged_in()
	{
		$user = $this->createUser();
		Airlock::actingAs($this->createUser());

		$response = $this->expectJSON()->post("/api/v1/auth/login", [
			"email" => $user->email,
			"password" => "password",
			"device_name" => "test-device-name",
		]);

		$response->assertStatus(403);
		$response->assertJSONStructure($this->errorStructure());
		$this->assertDatabaseMissing("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}


	/////////////////////////
	//    REFRESH TOKEN    //
	/////////////////////////

	// REFRESH TOKEN
	/** @test */
	public function authenticated_users_can_refresh_their_active_token()
	{
		$this->withoutExceptionHandling();
		// LOG IN
		$user = $this->createUser();
		$response = $this->expectJSON()->post("/api/v1/auth/login", [
			"email" => $user->email,
			"password" => "password",
			"device_name" => "test-device-name",
		]);

		// REFRESH TOKEN
		Airlock::actingAs($user);
		$response = $this->expectJSON()->patch("/api/v1/auth/refresh", ["device_name" => "test-device-name"]);

		$response->assertStatus(200);
		$response->assertJSONStructure($this->userResourceStructure());
		$this->assertDatabaseHas("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}

	public function authenticated_users_cannot_refresh_an_unexisting_token()
	{
		$this->withoutExceptionHandling();
		// LOG IN
		$user = $this->createUser();
		$response = $this->expectJSON()->post("/api/v1/auth/login", [
			"email" => $user->email,
			"password" => "password",
			"device_name" => "test-device-name",
		]);

		// REFRESH TOKEN
		Airlock::actingAs($user);
		$response = $this->expectJSON()->patch("/api/v1/auth/refresh", ["device_name" => "test-device-name-2"]);

		$response->assertStatus(401);
		$response->assertJSONStructure($this->errorStructure());
		$this->assertDatabaseHas("personal_access_tokens", [
			"name" => "test-device-name",
		]);
		$this->assertDatabaseMissing("personal_access_tokens", [
			"name" => "test-device-name-2",
		]);
	}

	/** @test */
	public function unauthenticated_users_cannot_refresh_a_token()
	{
		$response = $this->expectJSON()->patch("/api/v1/auth/refresh");

		$response->assertStatus(401);
		$response->assertJSONStructure($this->errorStructure());
		$this->assertDatabaseMissing("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}


	//////////////////
	//    LOGOUT    //
	//////////////////

	/** @test */
	public function an_authenticated_user_can_logout()
	{
		$this->withoutExceptionHandling();
		$user = $this->createUser();
		$response = $this->expectJSON()->post("/api/v1/auth/login", [
			"email" => $user->email,
			"password" => "password",
			"device_name" => "test-device-name",
		]);

		Airlock::actingAs($user);
		$response = $this->expectJSON()->delete("/api/v1/auth/logout", ["device_name" => "test-device-name"]);

		$response->assertStatus(200);
		$response->assertJSONStructure([
			"success",
			"message",
			"data",
		]);
		$this->assertDatabaseMissing("personal_access_tokens", [
			"name" => "test-device-name",
		]);
	}

	/** @test */
	public function an_unauthenticated_user_cannot_logout()
	{
		$response = $this->expectJSON()->delete("/api/v1/auth/logout", ["device_name" => "test-device-name"]);

		$response->assertStatus(401);
		$response->assertJSONStructure($this->errorStructure());
	}


	///////////////////////////
	//    FORGOT PASSWORD    //
	///////////////////////////

	/** @test */
	public function an_authenticated_user_cannot_send_a_password_reset_link()
	{
		$this->withoutExceptionHandling();
		$user = $this->createUser();

		Airlock::actingAs($user);
		$response = $this->expectJSON()
							->post("/api/v1/auth/password/email", [
								"email" => $user->email,
							]);

		$response->assertStatus(403);
		$response->assertJSONStructure($this->errorStructure());
	}

	/** @test */
	public function an_unauthenticated_user_can_send_a_password_reset_link()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()
							->post("/api/v1/auth/password/email", [
								"email" => $user->email,
							]);

		$response->assertStatus(200);
		$response->assertJSONStructure([
			"success",
			"message",
		]);
	}

	/** @test */
	public function a_password_reset_link_can_only_be_requested_with_a_valid_email()
	{
		$response = $this->expectJSON()
							->post("/api/v1/auth/password/email", [
								"email" => "jane.doe",
							]);

		$response->assertStatus(422);
		$response->assertJSONStructure($this->errorStructure());
	}

	/** @test */
	public function an_unexisting_account_cannot_receive_a_password_reset_link()
	{
		$response = $this->expectJSON()
							->post("/api/v1/auth/password/email", [
								"email" => "hello@inexisting.example.com",
							]);

		$response->assertStatus(422);
		$response->assertJSONStructure($this->errorStructure());
	}


	///////////////////////////////////
	//    RESEND EMAIL VALIDATION    //
	///////////////////////////////////

	/** @test */
	public function an_authenticated_user_without_validated_email_can_resend_a_verification_email()
	{
		$response = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestData());

		$response->assertStatus(201);
		$response->assertJSONStructure($this->userResourceStructure());

		$this->assertDatabaseHas("users", [
			"username" => "johndoe",
		]);

		Airlock::actingAs(User::where('username', 'johndoe')->first());
		$response = $this->expectJSON()
							->post("/api/v1/auth/email/resend");

		$response->assertStatus(200);
		$response->assertJSONStructure([
			"success",
			"message",
		]);
	}

	/** @test */
	public function an_authenticated_user_with_validated_email_cannot_resend_a_verification_email_but_receives_a_confirmation_of_validation()
	{
		$user = $this->createUser();
		Airlock::actingAs($user);

		$response = $this->expectJSON()
							->post("/api/v1/auth/email/resend");

		$response->assertStatus(200);
		$response->assertJSONStructure([
			"success",
			"message",
		]);
	}

	/** @test */
	public function an_unauthorized_user_cannot_resend_a_verification_email()
	{
		$response = $this->expectJSON()->post("/api/v1/auth/email/resend");

		$response->assertStatus(401);
		$response->assertJSONStructure($this->errorStructure());
	}


	//////////////////////////
	//    DATA RETRIEVAL    //
	//////////////////////////

	/** @test */
	public function an_authenticated_user_can_retrieve_their_data()
	{
		$user = $this->createUser();
		Airlock::actingAs($user);

		$response = $this->expectJSON()->get("/api/v1/user");

		$response->assertStatus(200);
		$response->assertJSONStructure($this->userResourceStructure());
	}

	/** @test */
	public function a_user_cannot_get_data_after_registration_without_email_verification()
	{
		$response = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestData());

		$response->assertStatus(201);
		$response->assertJSONStructure($this->userResourceStructure());

		$this->assertDatabaseHas("users", [
			"username" => "johndoe",
		]);
		$this->assertDatabaseHas("personal_access_tokens", [
			"name" => "test-device-name",
		]);

		// Email not verified, authenticated
		Airlock::actingAs(User::where('username', 'johndoe')->first());
		$response = $this->expectJSON()
							->get("/api/v1/user");

		$response->assertStatus(403);
		$response->assertJSONStructure($this->errorStructure());
	}


	/** @test */
	public function an_unauthenticated_user_cannot_retrieve_their_data()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()->get("/api/v1/user");

		$response->assertStatus(401);
		$response->assertJSONStructure($this->errorStructure());
	}

	// 404, actually does not belong in this file
	/** @test */
	public function any_request_to_inexisting_page_returns_a_404()
	{
		$response = $this->expectJSON()->post("/api/v1/404");

		$response->assertStatus(404);
		$response->assertJSONStructure($this->errorStructure());
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

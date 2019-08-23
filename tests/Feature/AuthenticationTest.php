<?php

namespace Tests\Feature;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\APIHelpers;
use TravelCompanion\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, APIHelpers;

    // REGISTER
    /** @test */
    public function a_client_can_register_with_basic_information()
    {
        $this->withoutExceptionHandling();

        $response = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "home_location" => new Point(1.12548568, 24.25488965),
            "password" => "password",
        ]);

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "data",
        ]);
        $this->assertDatabaseHas("users", ["username" => "johndoe"]);
    }

    /** @test */
    public function a_client_can_register_with_additional_information()
    {
        $this->withoutExceptionHandling();

        $response = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "home_location" => "(1.12548568, 24.25488965)",
            "password" => "password",
        ]);

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "data",
        ]);
        $this->assertDatabaseHas("users", ["username" => "johndoe"]);
    }

    /** @test */
    public function a_client_cannot_register_with_incorrect_data()
    {

    }

    /** @test */
    public function additional_field_are_ignored_on_registration()
    {

    }

    /** @test */
    public function a_client_cannot_register_without_all_required_fields()
    {

    }

    // LOGIN
    /** @test */
    public function a_user_can_log_in()
    {
        $user = factory(User::class)->create();

        $response = $this->post("/api/v1/auth/login", [
            "email" => $user->email,
            "password" => "password",
        ]);

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "data" => [
                "token",
                "token_type",
                "expires_in",
                "user",
            ],
        ]);
    }

    /** @test */
    public function a_user_cannot_login_after_registration_without_email_verification()
    {

    }

    /** @test */
    public function a_user_cannot_log_in_with_wrong_credentials()
    {
        $user = factory(User::class)->create();

        $response = $this->post("/api/v1/auth/login", [
            "email" => $user->email,
            "password" => "pwd",
        ]);

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $response = $this->post("/api/v1/auth/login", [
            "email" => "abc@donttryme.com",
            "password" => "password",
        ]);

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    /** @test */
    public function a_user_cannot_login_whithout_email_and_password()
    {
        $user = factory(User::class)->create();

        $response = $this->expectJSON()->post("/api/v1/auth/login", [
            "password" => "password",
        ]);

        $response->assertStatus(422);
        $response->assertJSONStructure([
            "success",
            "message",
            "errors" => [
                "email",
            ],
        ]);

        $response = $this->expectJSON()->post("/api/v1/auth/login", [
            "email" => $user->email,
        ]);

        $response->assertStatus(422);
        $response->assertJSONStructure([
            "success",
            "message",
            "errors" => [
                "password",
            ],
        ]);
    }

    /** @test */
    public function additional_fields_are_ignored_on_login()
    {
        $user = factory(User::class)->create();

        $response = $this->expectJSON()->post("/api/v1/auth/login", [
            "email" => $user->email,
            "password" => "password",
            "additional_resource" => "Recipe for chocolate chip cookies :)",
       ]);

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "data" => [
                "token",
                "token_type",
                "expires_in",
                "user",
            ],
        ]);
    }

    /** @test */
    public function an_user_with_expired_token_cannot_retreive_their_data()
    {
        $this->expireTokens();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                        ->expectJSON()
                        ->get("/api/v1/user");

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message"
        ]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_retreive_their_data()
    {
        $user = factory(User::class)->create();

        $response = $this->expectJSON()->get("/api/v1/user");

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    /** @test */
    public function a_user_with_valid_token_can_retreive_their_data()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->expectJSON()->get("/api/v1/user");

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "data",
        ]);
    }

    // REFRESH TOKEN
    /** @test */
    public function authenticated_users_can_refresh_their_token()
    {

    }

    /** @test */
    public function unauthenticated_users_cannot_refresh_their_token()
    {

    }

    // LOGOUT
    /** @test */
    public function an_authenticated_user_can_logout()
    {

    }

    /** @test */
    public function a_user_cannot_receive_data_after_logout()
    {

    }

    /** @test */
    public function an_unauthenticated_user_cannot_logout()
    {

    }

    /** @test */
    public function any_request_to_inexisting_page_returns_a_404()
    {
        $response = $this->expectJSON()->post("/api/v1/404");

        $response->assertStatus(404);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }
}

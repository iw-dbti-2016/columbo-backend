<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\APIHelpers;
use TravelCompanion\Currency;
use TravelCompanion\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, APIHelpers;

    // REGISTER
    /** @test */
    public function a_client_can_register_with_basic_information()
    {
        $response = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "data",
        ]);
        $this->assertDatabaseHas("users", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com"
        ]);
    }

    /** @test */
    public function a_client_cannot_register_without_all_required_fields()
    {
        $responses = [];

        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "username" => "johndoe",
            "email" => "john@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "last_name" => "Doe",
            "username" => "johndoe",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
        ]);

        foreach ($responses as $i => $response) {
            $response->assertStatus(422);
            $response->assertJSONStructure([
                "success",
                "message",
                "errors",
            ]);

            $this->assertDatabaseMissing("users", [
                "first_name" => "John",
                "last_name" => "Doe",
                "username" => "johndoe",
                "email" => "john@example.com"
            ]);
        }
    }

    /** @test */
    public function a_client_cannor_register_with_wrong_data()
    {
        $this->withoutExceptionHandling();

        $responses = [];

        // First name wrong characters [A-Za-z-']{2,50}
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "Johnnythebest!!",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe1",
            "email" => "john1@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // First name too long
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "JohnDoeTheBestWithAnExtremelyUnneccesaryLongNameWantsToRegisterForThisApplicationTooPleaseLetMeInIAmSoHypedRightNow",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe2",
            "email" => "john2@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // First name too short
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "J",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe3",
            "email" => "john3@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Middle name wrong characters [A-Za-z-'. ]{0,100} (little more complicated regex: points only after word group etc)
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "#R.",
            "last_name" => "Doe",
            "username" => "johndoe4",
            "email" => "john4@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Middle name too long
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert",
            "last_name" => "Doe",
            "username" => "johndoe5",
            "email" => "john5@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // First name wrong characters [A-Za-z-']{2,50}
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe45",
            "username" => "johndoe",
            "email" => "john6@example6.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Last name too short
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "D",
            "username" => "johndoe7",
            "email" => "john7@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Last name too long
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "DoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoe",
            "username" => "johndoe8",
            "email" => "john8@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Username wrong characters [A-Za-z0-9-.]{4,40}
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe9 :)",
            "email" => "john9@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Username too short
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "jd",
            "email" => "john10@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Username too long
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoejohndoejohndoejohndoejohndoejohndoe11",
            "email" => "john11@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Invalid email
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe12",
            "email" => "john12example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Invalid email
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe13",
            "email" => "john13@example@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Email too long (max 80)
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe14",
            "email" => "johnjohnjohnjohnjohnjohn14@examplexamplexamplexamplexamplexamplexample.comcomcomcom",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Wrong location format
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe15",
            "email" => "john15@example.com",
            "home_location" => "-99 5454.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Location wrong characters
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe16",
            "email" => "john16@example.com",
            "home_location" => "(50,8550625;4,3053505)",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Password too short .*{}
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe17",
            "email" => "john17@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "pw",
            "password_confirmation" => "pw",
        ]);

        // Password confirmation not matching
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe18",
            "email" => "john18@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "pass",
        ]);

        foreach ($responses as $i => $response) {
            $response->assertStatus(422);
            $response->assertJSONStructure([
                "success",
                "message",
                "errors",
            ]);

            $this->assertDatabaseMissing("users", [
                "first_name" => "John",
                "last_name" => "Doe",
            ]);
            $this->assertDatabaseMissing("users", [
                "username" => "johndoe" . $i,
            ]);
        }
    }

    /** @test */
    public function additional_field_are_ignored_on_registration()
    {
        $response = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
            "grandfather_last_name" => "Doeoe",
            "grandmother_favorite_color" => "brown, but not brown brown, rather brown-red-ish",
        ]);

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "data",
        ]);

        $this->assertDatabaseHas("users", [
            "username" => "johndoe",
        ]);
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
        $this->withoutExceptionHandling();

        $response = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "home_location" => "50.8550625 4.3053505",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "data",
        ]);

        $this->assertDatabaseHas("users", [
            "username" => "johndoe",
        ]);

        $response = $this->post("/api/v1/auth/login", [
            "email" => "john@example.com",
            "password" => "password",
        ]);

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
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

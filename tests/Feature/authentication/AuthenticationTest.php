<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Currency;
use TravelCompanion\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    // REGISTER
    /** @test */
    public function a_client_can_register_with_basic_information()
    {
        $response = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestData());

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
        $required_fields = ["first_name", "last_name", "username", "email", "password", "password_confirmation"];

        $responses = [];

        foreach ($required_fields as $field) {
            $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWithout($field));
        }

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
    public function a_client_cannot_register_with_wrong_data()
    {
        $responses = [];

        // First name wrong characters [A-Za-z-']{2,50}
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "first_name" => "Johnnythebest!!"
        ]));

        // First name too long
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "first_name" => "JohnDoeTheBestWithAnExtremelyUnneccesaryLongNameWantsToRegisterForThisApplicationTooPleaseLetMeInIAmSoHypedRightNow",
        ]));

        // First name too short
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "first_name" => "J",
        ]));

        // Middle name wrong characters [A-Za-z-'. ]{0,100} (little more complicated regex: points only after word group etc)
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "middle_name" => "#R.",
        ]));

        // Middle name too long
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "middle_name" => "Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert",
        ]));

        // Last name wrong characters [A-Za-z-']{2,50}
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "last_name" => "Doe45",
        ]));

        // Last name too short
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "last_name" => "D",
        ]));

        // Last name too long
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "last_name" => "DoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoe",
        ]));

        // Username wrong characters [A-Za-z0-9-.]{4,40}
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "username" => "johndoe9 :)",
        ]));

        // Username too short
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "username" => "jd",
        ]));

        // Username too long
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "username" => "johndoejohndoejohndoejohndoejohndoejohndoe11",
        ]));

        // Invalid email
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "email" => "john12example.com",
        ]));

        // Invalid email
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "email" => "john13@example@example.com",
        ]));

        // Email too long (max 80)
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "email" => "johnjohnjohnjohnjohnjohn14@examplexamplexamplexamplexamplexamplexample.comcomcomcom",
        ]));

        // Password too short .*{}
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "password" => "pw",
            "password_confirmation" => "pw",
        ]));

        // Password confirmation not matching
        $responses[] = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "password" => "password",
            "password_confirmation" => "pass",
        ]));

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
        $response = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestDataWith([
            "birth_date" => "2019-01-01",
            "grandfather_last_name" => "Doedoe",
            "grandmother_favorite_color" => "yellow, but not yellow yellow, rather yellow-red-ish",
        ]));

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "data",
        ]);

        $this->assertDatabaseHas("users", [
            "username" => "johndoe",
        ]);

        $this->assertDatabaseMissing("users", [
            "birth_date" => "2019-01-01",
        ]);
    }

    // LOGIN
    /** @test */
    public function a_user_can_log_in()
    {
        $user = factory(User::class)->create();

        $response = $this->expectJSON()->post("/api/v1/auth/login", [
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
    public function a_user_cannot_get_data_after_registration_without_email_verification()
    {
        $response = $this->expectJSON()->post("/api/v1/auth/register", $this->getTestData());

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "data" => [
                "token",
                "token_type",
                "expires_in",
                "user",
            ],
        ]);

        $this->assertDatabaseHas("users", [
            "username" => "johndoe",
        ]);

        // Unauthenticated
        $response = $this->expectJSON()->get("/api/v1/user");

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        // Email not verified, authenticated
        $response = $this->expectJSON()
                            ->actingAs(User::where('username', 'johndoe')->first())
                            ->get("/api/v1/user");

        $response->assertStatus(403);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    /** @test */
    public function a_user_cannot_log_in_with_wrong_credentials()
    {
        $user = factory(User::class)->create();

        $response = $this->expectJSON()->post("/api/v1/auth/login", [
            "email" => $user->email,
            "password" => "pwd",
        ]);

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $response = $this->expectJSON()->post("/api/v1/auth/login", [
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
            "birth_date" => "2019-01-01",
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
    public function authenticated_users_can_refresh_their_active_token()
    {
        $user = factory(User::class)->create();

        $response = $this->expectJSON()->actingAs($user)->patch("/api/v1/auth/refresh");

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
    public function authenticated_users_cannot_refresh_their_expired_token()
    {
        $this->expireTokens();

        $user = factory(User::class)->create();
        $response = $this->expectJSON()->actingAs($user)->patch("/api/v1/auth/refresh");

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    /** @test */
    public function unauthenticated_users_cannot_refresh_their_token()
    {
        $response = $this->expectJSON()->patch("/api/v1/auth/refresh");

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    // LOGOUT
    /** @test */
    public function an_authenticated_user_can_logout()
    {
        $user = factory(User::class)->create();
        $response = $this->expectJSON()->actingAs($user)->delete("/api/v1/auth/logout");

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "message",
            "data",
        ]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_logout()
    {
        $response = $this->expectJSON()->delete("/api/v1/auth/logout");

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    // FORGOT PASSWORD
    /** @test */
    public function an_authenticated_user_cannot_send_a_password_reset_link()
    {
        $user = factory(User::class)->create();

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/auth/password/email", [
                                "email" => $user->email,
                            ]);

        $response->assertStatus(403);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    /** @test */
    public function an_unauthenticated_user_can_send_a_password_reset_link()
    {
        $user = factory(User::class)->create();

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
    public function a_password_reset_link_can_only_be_requested_with_valid_data()
    {
        $user = factory(User::class)->create();

        $response = $this->expectJSON()
                            ->post("/api/v1/auth/password/email", [
                                "email" => "jane.doe",
                            ]);

        $response->assertStatus(422);
        $response->assertJSONStructure([
            "success",
            "message",
            "errors",
        ]);
    }

    /** @test */
    public function an_unexisting_account_cannot_receive_a_password_reset_link()
    {
        $response = $this->expectJSON()
                            ->post("/api/v1/auth/password/email", [
                                "email" => "hello@inexisting.example.com",
                            ]);

        $response->assertStatus(422);
        $response->assertJSONStructure([
            "success",
            "errors" => [
                "email",
            ],
        ]);
    }

    // RESEND EMAIL VALIDATION
    /** @test */
    public function an_authorized_user_without_validated_email_can_resend_a_verification_email()
    {
        $response = $this->expectJSON()->post("/api/v1/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "data" => [
                "token",
                "token_type",
                "expires_in",
                "user",
            ],
        ]);

        $this->assertDatabaseHas("users", [
            "username" => "johndoe",
        ]);

        $response = $this->expectJSON()
                            ->actingAs(User::where('username', 'johndoe')->first())
                            ->post("/api/v1/auth/email/resend");

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    /** @test */
    public function an_authorized_user_with_validated_email_cannot_resend_a_verification_email_but_receives_a_confirmation_af_validation()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $response = $this->expectJSON()
                            ->actingAs($user)
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
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    // 404, actually does not belong in this file
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

    private function getTestData($replacements=[])
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

    private function getTestDataWith($replacements=[])
    {
        return array_merge($this->getTestData(), $replacements);
    }

    private function getTestDataWithout($unset)
    {
        $array = $this->getTestData();

        if (is_array($unset)) {
            foreach ($unset as $field) {
                unset($array[$field]);
            }
        } else {
            unset($array[$unset]);
        }

        return $array;
    }
}

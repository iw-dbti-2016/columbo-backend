<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\Traits\TestHelpers;
use TravelCompanion\User;

class AuthenticationWebTest extends TestCase
{
    use RefreshDatabase, TestHelpers;

    // REGISTER
    /** @test */
    public function a_client_can_register_with_basic_information()
    {
        $response = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $response->assertRedirect('/auth/email/verify');

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

        $responses[] = $this->post("/auth/register", [
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "username" => "johndoe",
            "email" => "john@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "last_name" => "Doe",
            "username" => "johndoe",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "password_confirmation" => "password",
        ]);

        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "password" => "password",
        ]);

        foreach ($responses as $i => $response) {
            $response->assertStatus(302);
            $response->assertSessionHasErrors();
        }

        $this->assertDatabaseMissing("users", [
            "first_name" => "John",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com"
        ]);
    }

    /** @test */
    public function a_client_cannot_register_with_wrong_data()
    {
        $responses = [];

        // First name wrong characters [A-Za-z-']{2,50}
        $responses[] = $this->post("/auth/register", [
            "first_name" => "Johnnythebest!!",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe1",
            "email" => "john1@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // First name too long
        $responses[] = $this->post("/auth/register", [
            "first_name" => "JohnDoeTheBestWithAnExtremelyUnneccesaryLongNameWantsToRegisterForThisApplicationTooPleaseLetMeInIAmSoHypedRightNow",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe2",
            "email" => "john2@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // First name too short
        $responses[] = $this->post("/auth/register", [
            "first_name" => "J",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe3",
            "email" => "john3@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Middle name wrong characters [A-Za-z-'. ]{0,100} (little more complicated regex: points only after word group etc)
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "#R.",
            "last_name" => "Doe",
            "username" => "johndoe4",
            "email" => "john4@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Middle name too long
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert Robert",
            "last_name" => "Doe",
            "username" => "johndoe5",
            "email" => "john5@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // First name wrong characters [A-Za-z-']{2,50}
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe45",
            "username" => "johndoe",
            "email" => "john6@example6.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Last name too short
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "D",
            "username" => "johndoe7",
            "email" => "john7@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Last name too long
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "DoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoeDoe",
            "username" => "johndoe8",
            "email" => "john8@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Username wrong characters [A-Za-z0-9-.]{4,40}
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe9 :)",
            "email" => "john9@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Username too short
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "jd",
            "email" => "john10@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Username too long
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoejohndoejohndoejohndoejohndoejohndoe11",
            "email" => "john11@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Invalid email
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe12",
            "email" => "john12example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Invalid email
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe13",
            "email" => "john13@example@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        // Email too long (max 80)
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe14",
            "email" => "johnjohnjohnjohnjohnjohn14@examplexamplexamplexamplexamplexamplexample.comcomcomcom",
            "password" => "password",
            "password_confirmation" => "password",
        ]);


        // Password too short .*{}
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe17",
            "email" => "john17@example.com",
            "password" => "pw",
            "password_confirmation" => "pw",
        ]);

        // Password confirmation not matching
        $responses[] = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe18",
            "email" => "john18@example.com",
            "password" => "password",
            "password_confirmation" => "pass",
        ]);

        foreach ($responses as $i => $response) {
            $response->assertStatus(302);
            $response->assertSessionHasErrors();
        }

        $this->assertDatabaseMissing("users", [
            "first_name" => "John",
            "last_name" => "Doe",
        ]);

        $this->assertDatabaseMissing("users", [
            "username" => "johndoe" . $i,
        ]);
    }

    /** @test */
    public function additional_field_are_ignored_on_registration()
    {
        $response = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "password" => "password",
            "password_confirmation" => "password",
            "birth_date" => "2019-01-01",
            "grandfather_last_name" => "Doeoe",
            "grandmother_favorite_color" => "brown, but not brown brown, rather brown-red-ish",
        ]);

        $response->assertRedirect("/auth/email/verify");
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas("users", [
            "username" => "johndoe",
        ]);

        $this->assertDatabaseMissing("users", [
            "birth_date" => "2019-01-01",
        ]);
    }

    // LOGIN
    /** @test */
    public function an_unauthenticated_user_can_see_login_page()
    {
        $response = $this->get("/auth/login");

        $response->assertStatus(200);
    }

    /** @test */
    public function an_authenticated_user_cannot_see_login_page()
    {
        $user = factory(User::class)->create();

        $response = $this->callAsUser($user, "GET", "/auth/login");

        $response->assertRedirect("/app");
    }

    /** @test */
    public function a_user_can_log_in()
    {
        $user = factory(User::class)->create();

        $response = $this->post("/auth/login", [
            "email" => $user->email,
            "password" => "password",
        ]);

        $response->assertRedirect("/app");

        $response->assertCookie(config("api.jwt_payload_cookie_name"));
        $response->assertCookie(config("api.jwt_sign_cookie_name"));
    }

    /** @test */
    public function a_user_cannot_access_app_after_registration_without_email_verification()
    {
        $response = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $response->assertRedirect("/auth/email/verify");
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas("users", [
            "username" => "johndoe",
        ]);

        $response = $this->followingRedirects()->post("/auth/login", [
            "email" => "john@example.com",
            "password" => "password",
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertViewIs("auth.verify");
    }

    /** @test */
    public function a_user_cannot_log_in_with_wrong_credentials()
    {
        $user = factory(User::class)->create();

        $response = $this->post("/auth/login", [
            "email" => $user->email,
            "password" => "pwd",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $response = $this->post("/auth/login", [
            "email" => "abc@donttryme.com",
            "password" => "password",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function a_user_cannot_login_whithout_email_and_password()
    {
        $user = factory(User::class)->create();

        $response = $this->post("/auth/login", [
            "password" => "password",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $response = $this->post("/auth/login", [
            "email" => $user->email,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function additional_fields_are_ignored_on_login()
    {
        $user = factory(User::class)->create();

        $response = $this->post("/auth/login", [
            "email" => $user->email,
            "password" => "password",
            "birth_date" => "2019-01-01",
            "additional_resource" => "Recipe for chocolate chip cookies :)",
       ]);

        $response->assertRedirect("/app");
        $response->assertCookie(config("api.jwt_payload_cookie_name"));
        $response->assertCookie(config("api.jwt_sign_cookie_name"));
    }


    // LOGOUT
    /** @test */
    public function an_authenticated_user_can_log_out()
    {
        $user = factory(User::class)->create();

        $response = $this->callAsUser($user, "POST", "/auth/logout");

        $response->assertRedirect("/auth/login");
        $response->assertCookieExpired(config("api.jwt_sign_cookie_name"));
        $response->assertCookieExpired(config("api.jwt_payload_cookie_name"));
    }

    /** @test */
    public function an_unauthenticated_user_cannot_log_out()
    {
        $response = $this->post("/auth/logout");

        $response->assertRedirect("/auth/login");
    }

    // REFRESH TOKEN
    /** @test */
    public function an_authenticated_user_can_refresh_the_valid_token()
    {
        $user = factory(User::class)->create();

        $response = $this->callAsUser($user, "PATCH", "/api/v1/auth/refresh");

        $response->assertStatus(200);
        $response->assertCookie(config("api.jwt_sign_cookie_name"));
        $response->assertCookie(config("api.jwt_payload_cookie_name"));
    }

    // FORGOT PASSWORD
    /** @test */
    public function an_authenticated_user_cannot_send_a_password_reset_link()
    {
        $user = factory(User::class)->create();

        $response = $this->callAsUser($user, "POST", "/auth/password/email", [
                                "email" => $user->email,
                            ]);

        $response->assertRedirect("/app");
    }

    /** @test */
    public function an_unauthenticated_user_can_send_a_password_reset_link()
    {
        $user = factory(User::class)->create();

        $response = $this->post("/auth/password/email", [
                        "email" => $user->email,
                    ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');
    }

    /** @test */
    public function an_unexisting_account_cannot_receive_a_password_reset_link()
    {
        $response = $this->post("/auth/password/email", [
                        "email" => "hello@inexisting.example.com",
                    ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    // RESET PASSWORD
    /** @test */
    public function an_authenticated_user_cannot_see_reset_password_form()
    {
        $user = factory(User::class)->create();

        $response = $this->callAsUser($user, "GET", "/auth/password/reset/token");

        $response->assertRedirect("/app");
    }

    /** @test */
    public function an_authenticated_user_cannot_reset_password()
    {
        $user = factory(User::class)->create();

        $response = $this->callAsUser($user, "POST", "/auth/password/reset", [
                        "token" => "abcdef",
                        "email" => $user->email,
                        "password" => "password2",
                        "password_confirmation" => "password2",
                    ]);

        $response->assertRedirect("/app");
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
        $user = factory(User::class)->create();

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
        $user = factory(User::class)->create();

        $response = $this->post("/auth/password/email", [
            'email' => $user->email,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');

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
        $response->assertSessionHas('status');

        $this->assertTrue(Hash::check("password2", User::where('email', $user->email)->first()->password));
    }

    // RESEND EMAIL VALIDATION
    /** @test */
    public function an_authorized_user_without_validated_email_can_resend_a_verification_email()
    {
        $response = $this->post("/auth/register", [
            "first_name" => "John",
            "middle_name" => "R.",
            "last_name" => "Doe",
            "username" => "johndoe",
            "email" => "john@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $response->assertRedirect("/auth/email/verify");

        $this->assertDatabaseHas("users", [
            "username" => "johndoe",
        ]);

        $response = $this->callAsUser(User::where('username', 'johndoe')->first(), "GET", "/auth/email/resend");

        $response->assertStatus(302);
        $response->assertSessionHas('resent');
    }

    /** @test */
    public function an_authorized_user_with_validated_email_cannot_resend_a_verification_email_but_receives_a_confirmation_af_validation()
    {
        $user = factory(User::class)->create();

        $response = $this->callAsUser($user, "GET", "/auth/email/resend");

        $response->assertStatus(302);
        $response->assertSessionHas("verified");
    }

    /** @test */
    public function an_unauthorized_user_cannot_resend_a_verification_email()
    {
        $response = $this->get("/auth/email/resend");

        $response->assertRedirect("/auth/login");
    }

    // 404, actually does not belong in this file
    /** @test */
    public function any_request_to_inexisting_page_returns_a_404()
    {
        $response = $this->post("/404");

        $response->assertStatus(404);
    }
}

<?php

namespace TravelCompanion\Http\Controllers\Auth;

use TravelCompanion\Http\Controllers\Controller;
use TravelCompanion\Traits\Auth\SendsPasswordResetEmailsWithToken;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmailsWithToken;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['withoutTokenCookies']);
    }
}

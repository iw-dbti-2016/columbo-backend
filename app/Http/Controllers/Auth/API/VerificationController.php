<?php

namespace TravelCompanion\Http\Controllers\Auth\API;

use TravelCompanion\Http\Controllers\Controller;
use TravelCompanion\Traits\Auth\VerifiesEmailsWithToken;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmailsWithToken;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/auth/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:api'])->except('verify');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    protected function alreadyVerifiedResponse()
    {
        return response()->json([
            "success" => true,
            "message" => "Your email is already verified.",
        ], 200);
    }

    protected function OKResponse()
    {
        return response()->json([
            "success" => true,
            "message" => "Your email has been verified.",
        ], 200);
    }

    protected function OKResendResponse()
    {
        return response()->json([
            "success" => true,
            "message" => "The email has been re-sent.",
        ], 200);
    }

    protected function failedVerificationResponse()
    {
        return response()->json([
            "success" => true,
            "message" => "This link is invalid.",
        ], 403);
    }
}

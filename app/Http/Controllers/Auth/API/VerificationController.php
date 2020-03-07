<?php

namespace Columbo\Http\Controllers\Auth\API;

use Columbo\Http\Controllers\Controller;
use Columbo\Traits\APIResponses;
use Columbo\Traits\Auth\VerifiesEmailsWithToken;

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

    use VerifiesEmailsWithToken, APIResponses;

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
        return $this->okResponse("Your email is already verified.");
    }

    protected function OKResendResponse()
    {
        return $this->okResponse("The email has been re-sent.");
    }
}

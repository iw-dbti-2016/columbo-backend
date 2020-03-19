<?php

namespace Columbo\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Columbo\Http\Controllers\Controller;
use Columbo\Traits\APIResponses;
use Columbo\Traits\Auth\SendsPasswordResetEmailsWithToken;

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

    use SendsPasswordResetEmailsWithToken, APIResponses;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function __construct()
    {
    	$this->middleware('guest:airlock');
    }

    /**
     * Get the response for a failed validation
     *
     * @param  \Illuminate\Validation\Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendValidationFailedResponse(Validator $validator)
    {
        return $this->validationFailedResponse($validator);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $this->okResponse(trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return $this->validationFailedManualResponse([
                            "email" => trans($response),
                        ]);
    }
}

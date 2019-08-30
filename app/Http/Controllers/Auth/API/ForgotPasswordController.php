<?php

namespace TravelCompanion\Http\Controllers\Auth\API;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
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
    function __construct()
    {
    	$this->middleware('guest:api');
    }

    /**
     * Get the response for a failed validation
     *
     * @param  \Illuminate\Validation\Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendValidationFailedResponse(Validator $validator)
    {
        return response()->json([
        	"success" => false,
        	"message" => "Validation Failed",
        	"errors" => $validator->errors()->jsonSerialize(),
        ], 422);
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
        return response()->json([
        	"success" => true,
        	"message" => trans($response),
        ], 200);
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
        return response()->json([
        	"success" => false,
        	"message" => "Validation Failed",
        	"errors" => [
        		"email" => trans($response),
        	],
        ], 422);
    }
}

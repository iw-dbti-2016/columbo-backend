<?php

namespace TravelCompanion\Traits;

use Illuminate\Validation\Validator;

trait APIResponses
{
    public function resourceNotFoundResponse()
    {
        return response()->json([
            "success" => false,
            "message" => "Resource not found.",
        ], 404);
    }

    public function unauthorizedResponse()
    {
        return response()->json([
            "success" => false,
            "message" => "Unauthorized.",
        ], 403);
    }

    public function validationFailedResponse(Validator $validator)
    {
        return response()->json([
            "success" => false,
            "message" => "Validation failed.",
            "errors" => $validator->errors(),
        ], 422);
    }

    public function validationFailedManualResponse(array $errors)
    {
        return response()->json([
            "success" => false,
            "message" => "Validation failed.",
            "errors" => $errors,
        ], 422);
    }

    public function okResponse($message=null, $data=[], $statusCode=200)
    {
        $responseContent = [
            "success" => true,
            "data" => $data,
        ];

        if ($message != null) {
            $responseContent["message"] = $message;
        }

        return response()->json($responseContent, $statusCode);
    }

    public function failedResponse($message=null, $data=[], $statusCode=400)
    {
        $responseContent = [
            "success" => false,
            "data" => $data,
        ];

        if ($message != null) {
            $responseContent["message"] = $message;
        }

        return response()->json($responseContent, $statusCode);
    }
}

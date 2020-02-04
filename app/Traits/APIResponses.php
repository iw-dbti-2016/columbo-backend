<?php

namespace TravelCompanion\Traits;

use Illuminate\Validation\Validator;

trait APIResponses
{
	public function unauthenticatedResponse($msg)
	{
		return response()->json([
			"errors" => [
				[
					"status" => "401",
					"title" => "Unauthenticated",
					"detail"  => $msg,
				],
			],
		], 401);
	}

	public function resourceNotFoundResponse()
	{
		return response()->json([
			"success" => false,
			"message" => "Resource not found.",
		], 404);
	}

	public function unauthorizedResponse($msg="You are not authorized to view this.")
	{
		return response()->json([
			"errors" => [
				[
					"status" => "403",
					"title"  => "Not authorized",
					"detail" => $msg
				],
			],
		], 403);
	}

	public function badRequestResponse($msg="Something was wrong with your request.")
	{
		return response()->json([
			"errors" => [
				[
					"status" => "400",
					"title"  => "Bad Request",
					"detail" => $msg,
				],
			],
		], 400);
	}

	public function validationFailedResponse(Validator $validator)
	{
		return response()->json([
			"errors" => $validator->errors(),
		], 422);
	}

	public function validationFailedManualResponse(array $errors)
	{
		return response()->json([
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

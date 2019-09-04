<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Rules\Visibility;
use TravelCompanion\Trip;

class TripController extends Controller
{
    public function get(Trip $trip)
    {
        return response()->json([
            "success" => true,
            "data" => $trip->toArray(),
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|max:100",
            "synopsis" => "nullable|max:100",
            "description" => "nullable",
            "start_date" => "required|date_format:Y-m-d",
            "end_date" => "required|date_format:Y-m-d",
            "visibility" => ["required", new Visibility],
            "published_at" => "required|date_format:Y-m-d H:i:s",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Validation failed.",
                "errors" => $validator->errors(),
            ], 422);
        }

        $trip = $request->user()->tripsOwner()->create($request->all());

        return response()->json([
            "success" => true,
            "message" => "Trip successfully created.",
            "data" => $trip,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \TravelCompanion\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Trip $trip)
    {
        if ($trip->user_id != $request->user()->id) {
            return response()->json([
                "success" => false,
                "message" => "Unauthorized.",
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            "name" => "required|max:100",
            "synopsis" => "nullable|max:100",
            "description" => "nullable",
            "start_date" => "required|date_format:Y-m-d",
            "end_date" => "required|date_format:Y-m-d",
            "visibility" => ["required", new Visibility],
            "published_at" => "required|date_format:Y-m-d H:i:s",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Validation failed.",
                "errors" => $validator->errors(),
            ], 422);
        }

        $trip->update($request->all());

        return response()->json([
            "success" => true,
            "message" => "Trip succesfully updated",
            "data" => [],
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \TravelCompanion\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Trip $trip)
    {
        if ($trip->user_id != $request->user()->id) {
            return response()->json([
                "success" => false,
                "message" => "Unauthorized.",
            ], 403);
        }

        $trip->delete();

        return response()->json([
            "success" => true,
            "message" => "Trip successfully removed.",
        ], 200);
    }
}

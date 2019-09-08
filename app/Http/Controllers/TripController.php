<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Rules\Visibility;
use TravelCompanion\Traits\APIResponses;
use TravelCompanion\Trip;

class TripController extends Controller
{
    use APIResponses;

    public function get(Trip $trip)
    {
        return $this->okResponse(null, $trip->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validateData($request->all());

        if ($validator->fails()) {
            return $this->validationFailedResponse($validator);
        }

        $trip = $request->user()->tripsOwner()->create($request->all());

        return $this->okResponse("Trip successfully created.", $trip, 201);
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
            return $this->unauthorizedResponse();
        }

        $validator = $this->validateData($request->all());

        if ($validator->fails()) {
            return $this->validationFailedResponse($validator);
        }

        $trip->update($request->all());

        return $this->okResponse("Trip succesfully updated");
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
            return $this->unauthorizedResponse();
        }

        $trip->delete();

        return $this->okResponse("Trip successfully removed.");
    }

    private function validateData($data)
    {
        return Validator::make($data, [
            "name" => "required|max:100",
            "synopsis" => "nullable|max:100",
            "description" => "nullable",
            "start_date" => "required|date_format:Y-m-d",
            "end_date" => "required|date_format:Y-m-d",
            "visibility" => ["required", new Visibility],
            "published_at" => "required|date_format:Y-m-d H:i:s",
        ]);
    }
}

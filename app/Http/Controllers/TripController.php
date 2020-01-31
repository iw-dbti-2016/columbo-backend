<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Exceptions\AuthorizationException;
use TravelCompanion\Exceptions\ValidationException;
use TravelCompanion\Rules\Visibility;
use TravelCompanion\Traits\APIResponses;
use TravelCompanion\Trip;
use TravelCompanion\User;

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
		$this->validateData($request->all());

		$trip = $request->user()->tripsOwner()->create($request->all()["data"]["attributes"]);

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
		$this->ensureUserOwnsResourceOrFail($request->user(), $trip);
		$this->validateData($request->all());

		$trip->update($request->all()["data"]["attributes"]);

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
		$this->ensureUserOwnsResourceOrFail($request->user(), $trip);

		$trip->delete();

		return $this->okResponse("Trip successfully removed.");
	}

	private function validateData($data)
	{
		$validator = Validator::make($data, [
			"data.type"                    => "required|string|in:trip",
			"data.attributes.name"         => "required|max:100",
			"data.attributes.synopsis"     => "nullable|max:100",
			"data.attributes.description"  => "nullable",
			"data.attributes.start_date"   => "required|date_format:Y-m-d",
			"data.attributes.end_date"     => "required|date_format:Y-m-d",
			"data.attributes.visibility"   => ["required", new Visibility],
			"data.attributes.published_at" => "nullable|date_format:Y-m-d H:i:s",
		]);

		if ($validator->fails()) {
			throw new ValidationException($validator);
		}
	}

	private function ensureUserOwnsResourceOrFail(User $user, Model $resource)
	{
		if ($resource->user_id != $user->id) {
			throw new AuthorizationException();
		}
	}
}

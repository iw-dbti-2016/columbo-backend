<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Exceptions\AuthorizationException;
use TravelCompanion\Exceptions\BadRequestException;
use TravelCompanion\Exceptions\RequestStructureException;
use TravelCompanion\Exceptions\ValidationException;
use TravelCompanion\Http\Resources\Trip as TripResource;
use TravelCompanion\Rules\Visibility;
use TravelCompanion\Traits\APIResponses;
use TravelCompanion\Traits\RequestFormat;
use TravelCompanion\Trip;
use TravelCompanion\User;

class TripController extends Controller
{
	use APIResponses, RequestFormat;

	public function get(Trip $trip)
	{
		return new TripResource($trip);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$data = $request->all();

		$this->validateRequestStructureOrFail($data);
		$this->validateRelationshipsPresentOrFail($data, ["owner"]);
		$this->validateOwnerOrFail($request);

		$this->validateData($data);

		$trip = $request->user()->tripsOwner()->create($data["data"]["attributes"]);
		$trip->members()->attach($request->user(), [
			"join_date"		=> $data["data"]["attributes"]["start_date"],
			"leave_date"	=> $data["data"]["attributes"]["end_date"],
		]);

		return (new TripResource($trip))
					->response()
					->setStatusCode(201);
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

		return new TripResource($trip);
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

		return response()->json(["meta" => []], 200);
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

	private function validateOwnerOrFail(Request $request)
	{
		if ($request->all()["data"]["relationships"]["owner"]["type"] != "user" ||
			$request->all()["data"]["relationships"]["owner"]["id"] != $request->user()->id)
			throw new BadRequestException("The owner is not correct.");
	}

	private function ensureUserOwnsResourceOrFail(User $user, Model $resource)
	{
		if ($resource->user_id != $user->id) {
			throw new AuthorizationException();
		}
	}

	public function addMembers(Request $request, Trip $trip)
	{
		$data = $this->prepareMembers($request->all()["data"]);

		$trip->members()->syncWithoutDetaching($data);
	}

	public function removeMembers(Request $request, Trip $trip)
	{
		$ids = [];

		foreach ($request->all()["data"] as $member) {
			$ids[] = $member["id"];
		}

		$trip->members()->detach($ids);
	}

	private function prepareMembers($data)
	{
		$members = [];

		foreach ($data as $member) {
			$members[$member["id"]] = [
				"join_date"		=> $member["join_date"],
				"leave_date"	=> $member["leave_date"],
			];
		}

		return $members;
	}
}

<?php

namespace Columbo\Http\Controllers;

use Columbo\Exceptions\AuthorizationException;
use Columbo\Exceptions\BadRequestException;
use Columbo\Exceptions\RequestStructureException;
use Columbo\Exceptions\ResourceNotFoundException;
use Columbo\Exceptions\ValidationException;
use Columbo\Http\Resources\Trip as TripResource;
use Columbo\Http\Resources\TripCollection;
use Columbo\Rules\Visibility;
use Columbo\Traits\APIResponses;
use Columbo\Traits\RequestFormat;
use Columbo\Trip;
use Columbo\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
	use APIResponses, RequestFormat;

	public function list(Request $request)
	{
		$this->authorize('viewAny', Trip::class);

		return new TripCollection(Trip::all());
	}

	public function get(Trip $trip)
	{
		$this->authorize('view', $trip);

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
		$this->authorize('create', Trip::class);

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
	 * @param  \Columbo\Trip  $trip
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Trip $trip)
	{
		$this->authorize('update', $trip);

		$this->validateData($request->all());

		$trip->update($request->all()["data"]["attributes"]);

		return new TripResource($trip);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Columbo\Trip  $trip
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, Trip $trip)
	{
		$this->authorize('delete', $trip);

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

	public function addMembers(Request $request, Trip $trip)
	{
		$this->authorize('addMembers', $trip);

		$data = $this->prepareMembers($request->all()["data"]);

		$trip->members()->syncWithoutDetaching($data);
	}

	public function removeMembers(Request $request, Trip $trip)
	{
		$this->authorize('removeMembers', $trip);

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

	public function acceptInvite(Request $request, Trip $trip)
	{
		$this->authorize('acceptInvite', $trip);

		$trip->members()
			 ->updateExistingPivot(
			 	$request->user(),
			 	["invitation_accepted" => true]
			 );

		return response()->json([], 200);
	}

	public function declineInvite(Request $request, Trip $trip)
	{
		$this->authorize('declineInvite', $trip);

		$trip->members()->detach($request->user());

		return response()->json([], 200);
	}
}

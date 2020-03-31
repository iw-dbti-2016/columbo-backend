<?php

namespace Columbo\Http\Controllers;

use Columbo\Events\ResourceCreated;
use Columbo\Events\ResourceDeleted;
use Columbo\Events\ResourceUpdated;
use Columbo\Http\Requests\StoreTrip;
use Columbo\Http\Requests\UpdateTrip;
use Columbo\Http\Resources\Trip as TripResource;
use Columbo\Http\Resources\TripCollection;
use Columbo\Traits\APIResponses;
use Columbo\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
	use APIResponses;

	public function index()
	{
		$this->authorize('viewAny', Trip::class);

		return new TripCollection(Trip::all());
	}

	public function show(Trip $trip)
	{
		$this->authorize('view', $trip);

		return new TripResource($trip);
	}

	public function store(StoreTrip $request)
	{
		$trip = $request->user()->tripsOwner()->create($request->all());

		event(new ResourceCreated($request->user(), $trip));

		$trip->members()->attach($request->user(), [
			"role_label" => "owner",
			"join_date"  => $request->start_date,
			"leave_date" => $request->end_date,
		]);

		return (new TripResource($trip))
					->response()
					->setStatusCode(201);
	}

	public function update(UpdateTrip $request, Trip $trip)
	{
		$trip->update($request->all());

		event(new ResourceUpdated($request->user(), $user));

		return new TripResource($trip);
	}

	public function destroy(Request $request, Trip $trip)
	{
		$this->authorize('delete', $trip);

		$trip->delete();
		event(new ResourceDeleted($request->user(), $trip));

		return response()->json(["meta" => []], 200);
	}
}

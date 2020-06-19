<?php

namespace Columbo\Http\Controllers;

use Columbo\Events\ResourceCreated;
use Columbo\Events\ResourceDeleted;
use Columbo\Events\ResourceUpdated;
use Columbo\Http\Requests\StoreTrip;
use Columbo\Http\Requests\UpdateTrip;
use Columbo\Http\Resources\Locationable;
use Columbo\Http\Resources\Trip as TripResource;
use Columbo\Http\Resources\TripCollection;
use Columbo\POI;
use Columbo\Traits\APIResponses;
use Columbo\Trip;
use Grimzy\LaravelMysqlSpatial\Types\Point;
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

		return new TripResource($trip->load([
			'reports',
			'members' => function($query) {
				$query->withPivot('role_label');
			},
		]));
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

		event(new ResourceUpdated($request->user(), $trip));

		return new TripResource($trip);
	}

	public function destroy(Request $request, Trip $trip)
	{
		$this->authorize('delete', $trip);

		$trip->delete();
		event(new ResourceDeleted($request->user(), $trip));

		return response()->json(["meta" => []], 200);
	}

	public function locationables(Request $request, Trip $trip)
	{
		$request->validate([
			'coordinates.latitude'  => 'required|numeric|min:-90|max:90',
			'coordinates.longitude' => 'required|numeric|min:-180|max:180'
		]);

		$coordinate = new Point($request["coordinates"]["latitude"], $request["coordinates"]["longitude"]);

		$locations = $trip->locations()
				->distanceSphereValue('coordinates', $coordinate)
				->distanceSphere('coordinates', $coordinate, 250000)
				->limit(10)->get();

		$pois = POI::distanceSphereValue('coordinates', $coordinate)
				->distanceSphere('coordinates', $coordinate, 250000)
				->limit(10)->get();

		return Locationable::collection(
					$locations->merge($pois)
							->sortBy('distance')
				);
	}
}

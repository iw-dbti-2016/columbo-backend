<?php

namespace Columbo\Http\Controllers;

use Columbo\Events\ResourceCreated;
use Columbo\Events\ResourceDeleted;
use Columbo\Events\ResourceUpdated;
use Columbo\Http\Requests\StoreLocation;
use Columbo\Http\Requests\UpdateLocation;
use Columbo\Http\Resources\Location as LocationResource;
use Columbo\Http\Resources\LocationCollection;
use Columbo\Http\Resources\Locationable;
use Columbo\Location;
use Columbo\Trip;
use Illuminate\Http\Request;


class LocationController extends Controller
{
	public function index()
	{
		$this->authorize('viewAny', Location::class);

		return new LocationCollection(Location::all());
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(Trip $trip, Location $location)
	{
		$this->authorize('view', $location);

		return new LocationResource($location);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(StoreLocation $request, Trip $trip)
	{
		$location = new Location($request->all());

		$location->trip()->associate($trip);
		$location->owner()->associate($request->user());

		$location->save();

		event(new ResourceCreated($request->user(), $location));

		return (new Locationable($location))
					->response()
					->setStatusCode(201);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Columbo\Location  $location
	 * @return \Illuminate\Http\Response
	 */
	public function update(UpdateLocation $request, Trip $trip, Location $location)
	{
		$location->update($request->all());

		event(new ResourceUpdated($request->user(), $location));

		return new LocationResource($location);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Columbo\Location  $location
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, Trip $trip, Location $location)
	{
		$this->authorize('delete', $location);

		$location->delete();
		event(new ResourceDeleted($request->user(), $location));

		return response()->json(["meta" => []], 200);
	}
}

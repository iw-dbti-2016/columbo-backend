<?php

namespace TravelCompanion\Http\Controllers;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use TravelCompanion\Exceptions\AuthorizationException;
use TravelCompanion\Http\Resources\Location as LocationResource;
use TravelCompanion\Location;
use TravelCompanion\Traits\APIResponses;
use TravelCompanion\Traits\RequestFormat;
use TravelCompanion\User;

class LocationController extends Controller
{
    use APIResponses, RequestFormat;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Location $location)
    {
        return new LocationResource($location);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateOwnerRelationship($request->all(), $request->user());

    	$attributes = $request["data"]["attributes"];

        $attributes["coordinates"] = new Point($attributes["coordinates"][0], $attributes["coordinates"][1]);
        $location = new Location($attributes);
        $location->owner()->associate($request->user());
        $location->save();

        return (new LocationResource($location))
					->response()
					->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \TravelCompanion\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        $this->ensureUserOwnsResourceOrFail($request->user(), $location);

        $attributes = $request->all()["data"]["attributes"];
        $attributes["coordinates"] = new Point($attributes["coordinates"][0], $attributes["coordinates"][1]);

        $location->update($attributes);

        return new LocationResource($location);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \TravelCompanion\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Location $location)
    {
        $this->ensureUserOwnsResourceOrFail($request->user(), $location);

        $location->delete();

        return ["meta" => []];
    }

    private function ensureUserOwnsResourceOrFail(User $user, Model $resource)
    {
        if ($resource->user_id != $user->id) {
            throw new AuthorizationException();
        }
    }
}

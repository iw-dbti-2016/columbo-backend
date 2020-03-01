<?php

namespace TravelCompanion\Http\Controllers;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Exceptions\AuthorizationException;
use TravelCompanion\Exceptions\ValidationException;
use TravelCompanion\Http\Resources\Location as LocationResource;
use TravelCompanion\Location;
use TravelCompanion\Rules\Visibility;
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
    	$this->authorize('view', $location);

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
    	$this->authorize('create', Location::class);

        $this->validateOwnerRelationship($request->all(), $request->user());
        $this->validateData($request->all());

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
    	$this->authorize('update', $location);

        $this->validateData($request->all());

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
		$this->authorize('delete', $location);

        $location->delete();

        return ["meta" => []];
    }

    private function validateData($data)
    {
        $validator = Validator::make($data, [
            "data.type"                    => "required|string|in:location",
            "data.attributes.is_draft"     => "required|boolean",
            "data.attributes.coordinates"  => "required|array|size:2",
            "data.attributes.map_zoom"     => "required|numeric|min:0",
            "data.attributes.name"         => "required|max:100",
            "data.attributes.info"         => "nullable",
            "data.attributes.visibility"   => ["required", new Visibility],
            "data.attributes.published_at" => "nullable|date_format:Y-m-d H:i:s",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}

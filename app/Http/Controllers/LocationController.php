<?php

namespace TravelCompanion\Http\Controllers;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use TravelCompanion\Location;
use TravelCompanion\Traits\APIResponses;

class LocationController extends Controller
{
    use APIResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$attributes = $request["data"]["attributes"];

        $attributes["coordinates"] = new Point($attributes["coordinates"][0], $attributes["coordinates"][1]);
        $location = new Location($attributes);
        $location->owner()->associate($request->user());
        $location->save();

        return $this->okResponse("Location created successfully.", null, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \TravelCompanion\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \TravelCompanion\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \TravelCompanion\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        //
    }
}

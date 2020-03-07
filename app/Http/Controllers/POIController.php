<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Http\Request;
use TravelCompanion\Http\Resources\POICollection;
use TravelCompanion\Http\Resources\POI as POIResource;
use TravelCompanion\POI;

class POIController extends Controller
{
	public function list()
	{
		$this->authorize('viewAny', POI::class);

		return new POICollection(POI::all());
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function get(POI $poi)
	{
		$this->authorize('view', $poi);

		return new POIResource($poi);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \TravelCompanion\POI  $pOI
     * @return \Illuminate\Http\Response
     */
    public function show(POI $pOI)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \TravelCompanion\POI  $pOI
     * @return \Illuminate\Http\Response
     */
    public function edit(POI $pOI)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \TravelCompanion\POI  $pOI
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, POI $pOI)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \TravelCompanion\POI  $pOI
     * @return \Illuminate\Http\Response
     */
    public function destroy(POI $pOI)
    {
        //
    }
}

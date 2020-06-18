<?php

namespace Columbo\Http\Controllers;

use Columbo\Events\ResourceCreated;
use Columbo\Events\ResourceDeleted;
use Columbo\Events\ResourceUpdated;
use Columbo\Http\Requests\StoreSection;
use Columbo\Http\Requests\UpdateSection;
use Columbo\Http\Resources\Section as SectionResource;
use Columbo\Http\Resources\SectionCollection;
use Columbo\Report;
use Columbo\Section;
use Columbo\Trip;
use Illuminate\Http\Request;

class SectionController extends Controller
{
	public function index()
	{
		$this->authorize('viewAny', Section::class);

		$data = Section::orderBy('start_time', 'asc')
			->with(
				'report',//:id,date,trip_id',
				'report.trip',//:id,name',
				'owner',//:id,username',
				'locationable'
			)->get();
			// ->noDraft()
			// ->published()
			// ->orderRecent()

		return new SectionCollection($data);
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function show(Request $request, Trip $trip, Report $report, Section $section)
	{
		$this->authorize('view', $section);

		$data = $section
			->with('locationable:id,is_draft,coordinates,name,info,visibility')
			->with('owner:id,first_name,middle_name,last_name,username')
			->find($section->id);

		return new SectionResource($data);
	}

	/**
	* Store a newly created resource in storage.
	*
	* @param  StoreReport  $request
	* @return \Illuminate\Http\Response
	*/
	public function store(StoreSection $request, Trip $trip, Report $report)
	{
		$section = new Section($request->all());

		$section->owner()->associate($request->user());
		$section->report()->associate($report);

		$section->save();

		event(new ResourceCreated($request->user(), $section));

		return (new SectionResource($section))
				->response()
				->setStatusCode(201);
	}

	/**
	* Update the specified resource in storage.
	*
	* @param  UpdateSection  $request
	* @param  \Columbo\Section  $section
	* @return \Illuminate\Http\Response
	*/
	public function update(UpdateSection $request, Trip $trip, Report $report, Section $section)
	{
		$section->update($request->all());

		event(new ResourceUpdated($request->user(), $section));

		return new SectionResource($section->load('locationable', 'owner'));
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  \Columbo\Section  $section
	* @return \Illuminate\Http\Response
	*/
	public function destroy(Request $request, Trip $trip, Report $report, Section $section)
	{
		$this->authorize('delete', $section);

		$section->delete();
		event(new ResourceDeleted($request->user(), $section));

		return response()->json(["meta" => []], 200);
	}
}

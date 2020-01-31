<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Exceptions\AuthorizationException;
use TravelCompanion\Exceptions\ResourceNotFoundException;
use TravelCompanion\Exceptions\ValidationException;
use TravelCompanion\Report;
use TravelCompanion\Rules\Visibility;
use TravelCompanion\Section;
use TravelCompanion\Traits\APIResponses;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionController extends Controller
{
	use APIResponses;

	public function list()
	{
		// $this->ensureUrlCorrectnessOrFail($trip, $report);

		$data = Section::noDraft()
					->published()
					->orderRecent()
					->with('locationable:id,coordinates,name,info')
					->with('owner:id,first_name,middle_name,last_name,username')
					->get();

		return $this->okResponse(null, $data);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function get(Request $request, Section $section)
	{
		// $this->ensureUrlCorrectnessOrFail($trip, $report, $section);

		$data = $section
					->with('locationable:id,is_draft,coordinates,name,info,visibility')
					->with('owner:id,first_name,middle_name,last_name,username')
					->find($section->id);

		return $this->okResponse(null, $data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		// $this->ensureUrlCorrectnessOrFail($trip, $report);
		// $this->ensureUserOwnsResourceOrFail($request->user(), $report);
		$report = $this->retrieveReportOrFail($request);
		$this->ensureUserOwnsResourceOrFail($request->user(), $report);

		$this->validateData($request->all());

		$section = new Section($request->all()["data"]["attributes"]);

		$section->owner()->associate($request->user());
		$section->report()->associate(Report::find($request->all()["data"]["relationships"]["report"]["id"]));

		$section->save();

		return $this->okResponse("Section created successfully.", $section, 201);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \TravelCompanion\Section  $section
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Section $section)
	{
		// $this->ensureUrlCorrectnessOrFail($trip, $report, $section);
		$this->ensureUserOwnsResourceOrFail($request->user(), $section);

		$this->validateData($request->all());

		$section->update($request->all()["data"]["attributes"]);

		return $this->okResponse("Section successfully updated.", $section);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \TravelCompanion\Section  $section
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, Section $section)
	{
		// $this->ensureUrlCorrectnessOrFail($trip, $report, $section);
		$this->ensureUserOwnsResourceOrFail($request->user(), $section);

		$section->delete();

		return $this->okResponse("Section removed successfully.");
	}

	private function validateData($data)
	{
		$validator = Validator::make($data, [
			"data.type"                        => "required|string|in:section",
			"data.attributes.content"          => "nullable",
			"data.attributes.image"            => "nullable",
			"data.attributes.time"             => "nullable|date_format:H:i",
			"data.attributes.duration_minutes" => "nullable|integer",
			"data.attributes.visibility"       => ["required", new Visibility()],
			"data.attributes.published_at"     => "nullable|date_format:Y-m-d H:i:s",
		]);

		if ($validator->fails()) {
			throw new ValidationException($validator);
		}
	}

	private function retrieveReportOrFail($request)
	{
		$relationship_object = $request->all()["data"]["relationships"]["report"];

		return Report::findOrFail($relationship_object["id"]);
	}

	private function ensureUrlCorrectnessOrFail(Trip $trip, Report $report, Section $section=null)
	{
		if ($report->trip_id != $trip->id || ($section != null && $section->report_id != $report->id) ) {
			throw new ResourceNotFoundException();
		}
	}

	private function ensureUserOwnsResourceOrFail(User $user, Model $resource)
	{
		if ($resource->user_id != $user->id) {
			throw new AuthorizationException();
		}
	}
}

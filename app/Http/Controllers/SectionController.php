<?php

namespace Columbo\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Columbo\Exceptions\AuthorizationException;
use Columbo\Exceptions\ResourceNotFoundException;
use Columbo\Exceptions\ValidationException;
use Columbo\Http\Resources\Section as SectionResource;
use Columbo\Http\Resources\SectionCollection;
use Columbo\Report;
use Columbo\Rules\Visibility;
use Columbo\Section;
use Columbo\Traits\APIResponses;
use Columbo\Trip;
use Columbo\User;

class SectionController extends Controller
{
	use APIResponses;

	public function list()
	{
		$this->authorize('viewAny', Section::class);

		$data = Section::all();
		//noDraft()
					// ->published()
					// ->orderRecent()
					// ->with('locationable:id,coordinates,name,info')
					// ->with('owner:id,first_name,middle_name,last_name,username')
					// ->get();

		return new SectionCollection($data);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function get(Request $request, Section $section)
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
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$report = $this->retrieveReportOrFail($request);
		$this->authorize('create', [Section::class, $report]);

		$this->validateData($request->all());

		$section = new Section($request->all()["data"]["attributes"]);

		$section->owner()->associate($request->user());
		$section->report()->associate(Report::find($request->all()["data"]["relationships"]["report"]["id"]));

		$section->save();

		return (new SectionResource($section))
					->response()
					->setStatusCode(201);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Columbo\Section  $section
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Section $section)
	{
		$this->authorize('update', $section);

		$this->validateData($request->all());

		$section->update($request->all()["data"]["attributes"]);

		return new SectionResource($section);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Columbo\Section  $section
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, Section $section)
	{
		$this->authorize('delete', $section);

		$section->delete();

		return response()->json(["meta" => []], 200);
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
}

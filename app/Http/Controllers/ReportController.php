<?php

namespace Columbo\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Columbo\Exceptions\AuthorizationException;
use Columbo\Exceptions\ResourceNotFoundException;
use Columbo\Exceptions\ValidationException;
use Columbo\Http\Resources\Report as ReportResource;
use Columbo\Http\Resources\ReportCollection;
use Columbo\Report;
use Columbo\Rules\Visibility;
use Columbo\Traits\APIResponses;
use Columbo\Trip;
use Columbo\User;

class ReportController extends Controller
{
	use APIResponses;

	public function list()
	{
		$this->authorize('viewAny', Report::class);

		$reports = Report::with(
			'sections',
			'sections.locationable',
			'trip'
		)->get();

		return new ReportCollection($reports);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function get(Report $report)
	{
		$this->authorize('view', $report);

		return new ReportResource($report);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$trip = $this->retreiveTripOrFail($request);
		$this->authorize('create', [Report::class, $trip]);

		$this->validateDataOrFail($request->all());

		$report = new Report($request->all()["data"]["attributes"]);

		$report->trip()->associate($trip);
		$report->owner()->associate($request->user());

		$report->save();

		return (new ReportResource($report))
					->response()
					->setStatusCode(201);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Columbo\Report  $report
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Report $report)
	{
		$this->authorize('update', $report);

		$this->validateDataOrFail($request->all());

		$report->update($request->all()["data"]["attributes"]);

		return new ReportResource($report);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Columbo\Report  $report
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, Report $report)
	{
		$this->authorize('delete', $report);

		$report->delete();

		return response()->json(["meta" => []], 200);
	}

	private function validateDataOrFail($data)
	{
		$validator = Validator::make($data, [
			"data.attributes.title"        => "required|max:100",
			"data.attributes.date"         => "nullable|date_format:Y-m-d",
			"data.attributes.description"  => "nullable|max:5000",
			"data.attributes.visibility"   => ["required", new Visibility()],
			"data.attributes.published_at" => "nullable|date_format:Y-m-d H:i:s",
		]);

		if ($validator->fails()) {
			throw new ValidationException($validator);
		}
	}

	private function retreiveTripOrFail($request)
	{
		$relationship_object = $request->all()["data"]["relationships"]["trip"];

		return Trip::findOrFail($relationship_object["id"]);
	}
}

<?php

namespace Columbo\Http\Controllers;

use Columbo\Events\ResourceCreated;
use Columbo\Events\ResourceDeleted;
use Columbo\Events\ResourceUpdated;
use Columbo\Http\Requests\StoreReport;
use Columbo\Http\Requests\UpdateReport;
use Columbo\Http\Resources\Report as ReportResource;
use Columbo\Http\Resources\ReportCollection;
use Columbo\Report;
use Columbo\Traits\APIResponses;
use Columbo\Trip;
use Illuminate\Http\Request;

class ReportController extends Controller
{
	use APIResponses;

	public function index(Trip $trip)
	{
		$this->authorize('viewAny', [Report::class, $trip]);

		$reports = $trip->reports()->with([
			'sections' =>  function ($query) {
				$query->orderBy('start_time', 'asc');
			},
			'sections.locations',
			'sections.pois',
			'trip'
		])->get();

		return new ReportCollection($reports);
	}

	public function show(Trip $trip, Report $report)
	{
		$this->authorize('view', [$report, $trip]);

		return new ReportResource($report->load([
			'sections' => function($query) {
				$query->orderBy('start_time', 'asc');
			},
			'sections.owner',
			'sections.locations',
			'sections.pois',
		]));
	}

	public function store(StoreReport $request, Trip $trip)
	{
		$report = new Report($request->all());

		$report->trip()->associate($trip);
		$report->owner()->associate($request->user());

		$report->save();

		event(new ResourceCreated($request->user(), $report));

		return (new ReportResource($report))
					->response()
					->setStatusCode(201);
	}

	public function update(UpdateReport $request, Trip $trip, Report $report)
	{
		$report->update($request->all());

		event(new ResourceUpdated($request->user(), $report));

		return new ReportResource($report);
	}

	public function destroy(Request $request, Trip $trip, Report $report)
	{
		$this->authorize('delete', [$report, $trip]);

		$report->delete();
		event(new ResourceDeleted($request->user(), $report));

		return response()->json(["meta" => []], 200);
	}
}

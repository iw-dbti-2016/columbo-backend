<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Report;
use TravelCompanion\Rules\Visibility;
use TravelCompanion\Traits\APIResponses;
use TravelCompanion\Trip;

class ReportController extends Controller
{
    use APIResponses;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Trip $trip, Report $report)
    {
        if ($report->trip_id != $trip->id) {
            return $this->resourceNotFoundResponse();
        }

        return $this->okResponse(null, $report);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Trip $trip, Request $request)
    {
        if ($trip->user_id != $request->user()->id) {
            return $this->unauthorizedResponse();
        }

        $validator = $this->validateData($request->all());

        if ($validator->fails()) {
            return $this->validationFailedResponse($validator);
        }

        $report = new Report($request->all());

        $report->trip()->associate($trip);
        $report->owner()->associate($request->user());

        $report->save();

        return $this->okResponse("Report added.", $report->toArray(), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \TravelCompanion\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Trip $trip, Report $report)
    {
        if ($report->trip_id != $trip->id) {
            return $this->resourceNotFoundResponse();
        }

        if ($trip->user_id != $request->user()->id) {
            return $this->unauthorizedResponse();
        }

        $validator = $this->validateData($request->all());

        if ($validator->fails()) {
            return $this->validationFailedResponse($validator);
        }

        $report = $report->update($request->all());

        return $this->okResponse("Report was updated.", $report);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \TravelCompanion\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Trip $trip, Report $report)
    {
        if ($report->trip_id != $trip->id) {
            return $this->resourceNotFoundResponse();
        }

        if ($trip->user_id != $request->user()->id) {
            return $this->unauthorizedResponse();
        }

        $report->delete();

        return $this->okResponse("Report was deleted.");
    }

    private function validateData($data)
    {
        return Validator::make($data, [
            "title" => "required|max:100",
            "date" => "nullable|date_format:Y-m-d",
            "description" => "nullable|max:5000",
            "visibility" => ["required", new Visibility()],
            "published_at" => "required|date_format:Y-m-d H:i:s",
        ]);
    }
}

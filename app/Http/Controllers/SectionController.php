<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Report;
use TravelCompanion\Rules\Visibility;
use TravelCompanion\Section;
use TravelCompanion\Traits\APIResponses;
use TravelCompanion\Trip;

class SectionController extends Controller
{
    use APIResponses;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request, Trip $trip, Report $report, Section $section)
    {
        if ($section->report_id != $report->id || $report->trip_id != $trip->id) {
            return $this->resourceNotFoundResponse();
        }

        return $this->okResponse(null, $section);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Trip $trip, Report $report)
    {
        if ($report->trip_id != $trip->id) {
            return $this->resourceNotFoundResponse();
        }

        $validator = $this->validateData($request->all());

        if ($validator->fails()) {
            return $this->validationFailedResponse($validator);
        }

        $section = new Section($request->all());

        $section->owner()->associate($request->user());
        $section->report()->associate($report);

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
    public function update(Request $request, Trip $trip, Report $report, Section $section)
    {
        if ($section->report_id != $report->id || $report->trip_id != $trip->id) {
            return $this->resourceNotFoundResponse();
        }

        if ($section->user_id != $request->user()->id) {
            return $this->unauthorizedResponse();
        }

        $validator = $this->validateData($request->all());

        if ($validator->fails()) {
            return $this->validationFailedResponse($validator);
        }

        $section->update($request->all());

        return $this->okResponse("Section successfully updated.", $section);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \TravelCompanion\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Trip $trip, Report $report, Section $section)
    {
        if ($section->report_id != $report->id || $report->trip_id != $trip->id) {
            return $this->resourceNotFoundResponse();
        }

        if ($section->user_id != $request->user()->id) {
            return $this->unauthorizedResponse();
        }

        $section->delete();

        return $this->okResponse("Section removed successfully.");
    }

    private function validateData($data)
    {
        return Validator::make($data, [
            "content" => "nullable",
            "visibility" => ["required", new Visibility()],
            "published_at" => "required|date_format:Y-m-d H:i:s",
        ]);
    }
}

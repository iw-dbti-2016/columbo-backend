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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request, Trip $trip, Report $report, Section $section)
    {
        $this->ensureUrlCorrectnessOrFail($trip, $report, $section);

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
        $this->ensureUrlCorrectnessOrFail($trip, $report);
        $this->ensureUserOwnsResourceOrFail($request->user(), $report);
        $this->validateData($request->all());

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
        $this->ensureUrlCorrectnessOrFail($trip, $report, $section);
        $this->ensureUserOwnsResourceOrFail($request->user(), $section);

        $this->validateData($request->all());

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
        $this->ensureUrlCorrectnessOrFail($trip, $report, $section);
        $this->ensureUserOwnsResourceOrFail($request->user(), $section);

        $section->delete();

        return $this->okResponse("Section removed successfully.");
    }

    private function validateData($data)
    {
        $validator = Validator::make($data, [
            "content" => "nullable",
            "visibility" => ["required", new Visibility()],
            "published_at" => "required|date_format:Y-m-d H:i:s",
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
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

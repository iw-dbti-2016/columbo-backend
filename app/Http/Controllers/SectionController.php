<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Report;
use TravelCompanion\Rules\Visibility;
use TravelCompanion\Section;
use TravelCompanion\Trip;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request, Trip $trip, Report $report, Section $section)
    {
        if ($section->report_id != $report->id || $report->trip_id != $trip->id) {
            return response()->json([
                "success" => false,
                "message" => "Section not found.",
            ], 404);
        }

        return response()->json([
            "success" => true,
            "data" => $section,
        ], 200);
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
            return response()->json([
                "success" => false,
                "message" => "Page not found.",
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            "content" => "nullable",
            "visibility" => ["required", new Visibility()],
            "published_at" => "required|date_format:Y-m-d H:i:s",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Validation Failed.",
                "errors" => $validator->errors(),
            ], 422);
        }

        $section = new Section($request->all());

        $section->owner()->associate($request->user());
        $section->report()->associate($report);

        $section->save();

        return response()->json([
            "success" => true,
            "message" => "Section created successfully.",
            "data" => $section,
        ], 201);
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
            return response()->json([
                "success" => false,
                "message" => "Section not found.",
            ], 404);
        }

        if ($section->user_id != $request->user()->id) {
            return response()->json([
                "success" => false,
                "message" => "Unauthorized.",
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            "content" => "nullable",
            "visibility" => ["required", new Visibility()],
            "published_at" => "required|date_format:Y-m-d H:i:s",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Validation Failed.",
                "errors" => $validator->errors(),
            ], 422);
        }

        $section->update($request->all());

        return response()->json([
            "success" => true,
            "message" => "Section successfully updated.",
            "data" => $section,
        ]);
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
            return response()->json([
                "success" => false,
                "message" => "Section not found.",
            ], 404);
        }

        if ($section->user_id != $request->user()->id) {
            return response()->json([
                "success" => false,
                "message" => "Unauthorized.",
            ], 403);
        }

        $section->delete();

        return response()->json([
            "success" => true,
            "message" => "Section removed successfully.",
        ], 200);
    }
}

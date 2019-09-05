<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Report;
use TravelCompanion\Rules\Visibility;
use TravelCompanion\Trip;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Trip $trip, Report $report)
    {
        if ($report->trip_id != $trip->id) {
            return response()->json([
                "success" => true,
                "message" => "Report not found.",
            ], 404);
        }

        return response()->json([
            "success" => true,
            "data" => $report,
        ], 200);
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
            return response()->json([
                "success" => false,
                "message" => "Unauthorized.",
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            "title" => "required|max:100",
            "date" => "nullable|date_format:Y-m-d",
            "description" => "nullable|max:5000",
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

        $report = new Report($request->all());

        $report->trip()->associate($trip);
        $report->owner()->associate($request->user());

        $report->save();

        return response()->json([
            "success" => true,
            "message" => "Report added.",
            "data" => $report->toArray(),
        ], 201);
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
            return response()->json([
                "success" => true,
                "message" => "Report not found.",
            ], 404);
        }

        if ($trip->user_id != $request->user()->id) {
            return response()->json([
                "success" => false,
                "message" => "Unauthorized.",
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            "title" => "required|max:100",
            "date" => "nullable|date_format:Y-m-d",
            "description" => "nullable|max:5000",
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

        $report = $report->update($request->all());

        return response()->json([
            "success" => true,
            "message" => "Report was updated.",
            "data" => $report,
        ], 200);
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
            return response()->json([
                "success" => true,
                "message" => "Report not found.",
            ], 404);
        }

        if ($trip->user_id != $request->user()->id) {
            return response()->json([
                "success" => false,
                "message" => "Unauthorized.",
            ], 403);
        }

        $report->delete();

        return response()->json([
            "success" => true,
            "message" => "Report was deleted.",
        ], 200);
    }
}

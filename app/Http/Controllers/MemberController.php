<?php

namespace Columbo\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
{
	public function addMembers(Request $request, Trip $trip)
	{
		$this->authorize('addMembers', $trip);

		$data = $this->prepareMembers($request->all()["data"]);

		$trip->members()->syncWithoutDetaching($data);
	}

	public function updateMember(Request $request, Trip $trip)
	{
		// TODO
		return abort();
	}

	public function removeMembers(Request $request, Trip $trip)
	{
		$this->authorize('removeMembers', $trip);

		$ids = [];

		foreach ($request->all()["data"] as $member) {
			$ids[] = $member["id"];
		}

		$trip->members()->detach($ids);
	}

	private function prepareMembers($data)
	{
		$members = [];

		foreach ($data as $member) {
			$members[$member["id"]] = [
				"join_date"		=> $member["join_date"],
				"leave_date"	=> $member["leave_date"],
			];
		}

		return $members;
	}

	public function acceptMemberInvite(Request $request, Trip $trip)
	{
		$this->authorize('acceptInvite', $trip);

		$trip->members()
			 ->updateExistingPivot(
				$request->user(),
				["invitation_accepted" => true]
			 );

		return response()->json([], 200);
	}

	public function declineMemberInvite(Request $request, Trip $trip)
	{
		$this->authorize('declineInvite', $trip);

		$trip->members()->detach($request->user());

		return response()->json([], 200);
	}
}

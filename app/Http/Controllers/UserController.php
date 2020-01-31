<?php

namespace TravelCompanion\Http\Controllers;

use Illuminate\Http\Request;
use TravelCompanion\Traits\APIResponses;
use TravelCompanion\User;

class UserController extends Controller
{
    use APIResponses;

    /**
     * Display the specified resource.
     *
     * @param  \TravelCompanion\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->okResponse(null, $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \TravelCompanion\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \TravelCompanion\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function listTrips(Request $request)
    {
        return $this->okResponse(null, $request->user()->tripsOwner);
    }
}

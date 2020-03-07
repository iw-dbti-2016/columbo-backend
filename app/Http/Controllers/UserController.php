<?php

namespace Columbo\Http\Controllers;

use Illuminate\Http\Request;
use Columbo\Http\Resources\TripCollection;
use Columbo\Http\Resources\User as UserResource;
use Columbo\Traits\APIResponses;
use Columbo\User;

class UserController extends Controller
{
    use APIResponses;

    /**
     * Display the specified resource.
     *
     * @param  \Columbo\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Columbo\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Columbo\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function listTrips(Request $request)
    {
        return new TripCollection($request->user()->tripsOwner);
    }
}

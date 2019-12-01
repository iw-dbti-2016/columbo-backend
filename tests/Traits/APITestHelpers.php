<?php

namespace Tests\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

trait APITestHelpers
{
	/**
	 * Adds the Accept header to the request.
	 */
	public function expectJSON()
	{
		$this->withHeader('Accept', 'Application/json');

		return $this;
	}

	/**
	 * Sets the TTL for JWT tokens to 0.
	 * 	Used to test token expiry.
	 */
	public function expireTokens($ttl=0)
	{
        config(['jwt.ttl' => $ttl]);

        return $this;
	}

	/**
	 * Adds the required Authorization-header for running
	 * 	a test as a specific user.
	 * From: https://github.com/tymondesigns/jwt-auth/issues/1246#issuecomment-426705432
	 */
	public function actingAs(Authenticatable $user, $driver = null)
	{
		$token = "";

		try {
			$token = JWTAuth::fromUser($user);
		} catch (TokenExpiredException $e) {
			$token = config('JWT_EXPIRED_TOKEN');
		}

		$this->withHeader('Authorization', 'bearer ' . $token);

		return $this;
	}

	/////////////////////////////////////
	// STRUCTURE AND STATUS ASSERTIONS //
	/////////////////////////////////////

	protected function successStructure($message=true, $data=true)
	{
		$array = ["success"];

		if ($message)
			$array = array_merge($array, ["message"]);

		if ($data)
			$array = array_merge($array, ["data"]);

		return $array;
	}

	protected function successStructureWithoutData($message=true)
	{
		return $this->successStructure($message, false);
	}

	protected function errorStructure($message=true)
	{
		$array = [
			"success",
            "errors",
        ];

        if ($message)
        	$array = array_merge($array, ["message"]);

       	return $array;
	}

	protected function assertUnauthenticated($response)
	{
    	$response->assertStatus(401);
        $response->assertJSONStructure($this->successStructureWithoutData());
	}

	protected function assertUnauthorized($response)
	{
    	$response->assertStatus(403);
        $response->assertJSONStructure($this->successStructureWithoutData());
	}

	protected function assertNotFound($response)
	{
		$response->assertStatus(404);
		$response->assertJSONStructure($this->successStructureWithoutData());
	}

	protected function assertValidationFailed($response)
	{
		$response->assertStatus(422);
		$response->assertJSONStructure($this->errorStructure());
	}

	//////////////////////
	// ENTITY CREATIONS //
	//////////////////////

	protected function createUser($data=[])
	{
		return factory(User::class)->create($data);
	}

	protected function createTrip(User $user, $data=[])
	{
		return $user->tripsOwner()->save(factory(Trip::class)->make($data));
	}

	protected function createReport(User $user, Trip $trip, $data=[])
	{
		$report = factory(Report::class)->make($data);
        $report->owner()->associate($user);
        $report->trip()->associate($trip);
        $report->save();

		return $report;
	}

	protected function createSection(User $user, Report $report, $data=[])
	{
		$section = factory(Section::class)->make($data);
        $section->owner()->associate($user);
        $section->report()->associate($report);
        $section->save();

		return $section;
	}

	///////////////
	// TEST DATA //
	///////////////

	/**
	 * Returns the test-data with extra keys or
	 * 	specific keys overwritten.
	 *
	 * @param  Array	$replacement
	 * @return Array
	 */
    protected function getTestDataWith($replacement)
    {
    	return array_replace($this->getTestData(), $replacement);
    }

    /**
     * Return the test-data without soecified keys.
     *
     * @param  Array/String $unset
     * @return Array
     */
    protected function getTestDataWithout($unset)
    {
        $array = $this->getTestData();

        if (is_array($unset)) {
            foreach ($unset as $field) {
                unset($array[$field]);
            }
        } else {
            unset($array[$unset]);
        }

        return $array;
    }

	/** Must be overridden */
	protected function getTestData()
    {
    	return [];
    }
}

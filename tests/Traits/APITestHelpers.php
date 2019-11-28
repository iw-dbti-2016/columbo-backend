<?php

namespace Tests\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
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

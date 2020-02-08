<?php

namespace Tests\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Tests\Traits\ResourceFactory;
use TravelCompanion\Exceptions\TestException;
use Tymon\JWTAuth\Facades\JWTAuth;

trait TestHelpers
{
	use ResourceFactory;

	private $uri = "/";

	protected function setRefererUri($uri)
	{
		$this->uri = $uri;
	}

	public function ref($uri=null)
	{
		return $this->followingRedirects()
					->withHeader('referer', 'http://127.0.0.1:8000' . ($uri ?: $this->uri));
	}

	public function callAsUser(Authenticatable $user, $method, $uri, $parameters=[])
	{
		$token = JWTAuth::fromUser($user);
		$token = explode(".", $token);

		$cookies = [
			config("api.jwt_payload_cookie_name") => $token[0] . '.' . $token[1],
			config("api.jwt_sign_cookie_name") => Crypt::encrypt($token[2], false),
		];

		return $this->call($method, $uri, $parameters, $cookies);
	}

	///////////////////////
	// TEST DATA HELPERS //
	///////////////////////

	/**
	 * Returns the testdata.
	 *
	 * @param	Integer|null	$id
	 * @param	Array			$others	Extra items to add to the testdata
	 * @return	Array
	 */
	protected function getTestData($id=null, $others=[])
	{
		return $this->wrapInStructure([], $id, $others);
	}

	/**
	 * Returns the test-data with extra keys or
	 * 	specific keys overwritten.
	 *
	 * @param	Array			$replacement
	 * @param	Integer|null	$id
	 * @param	Array			$others			Extra items to add to the test-data
	 * @return	Array
	 */
	protected function getTestDataWith($replacement, $id=null, $others=[])
	{
		return $this->wrapInStructure($this->getTestAttributesWith($replacement), $id, $others);
	}

	/**
	 * Return the test-data without specified keys.
	 *
	 * @param	Array|String	$unset
	 * @param	Integer|null	$id
	 * @param	Array			$others			Extra items to add to the test-data
	 * @return	Array
	 */
	protected function getTestDataWithout($unset, $id=null, $others=[])
	{
		return $this->wrapInStructure($this->getTestAttributesWithout($unset), $id, $others);
	}

	/** Must be overridden */
	protected function getTestAttributes()
	{
		throw new TestException("Method getTestAttributes should be overridden.");
	}

	/**
	 * Returns the test-attributes with extra keys or
	 * 	specific keys overwritten.
	 *
	 * @param	Array	$replacement
	 * @return	Array
	 */
	protected function getTestAttributesWith($replacement)
	{
		return array_replace($this->getTestAttributes(), $replacement);
	}

	/**
	 * Returns the test-attributes without specified keys.
	 *
	 * @param	Array|String	$unset
	 * @return	Array
	 */
	protected function getTestAttributesWithout($unset)
	{
		$array = $this->getTestAttributes();

		if (is_array($unset)) {
			foreach ($unset as $field) {
				unset($array[$field]);
			}
		} else {
			unset($array[$unset]);
		}

		return $array;
	}

	/**
	 * Wraps the given attributes in the valid
	 * 	request-structure as specified by JSON:API.
	 * 	Others are simply appended if specified.
	 * 	The getTestAttributes-method is used when
	 * 	no attributes are specified.
	 *
	 * @param	Array			$attributes
	 * @param	Integer|null	$id
	 * @param	Array			$others		Extra data to merge in the structure.
	 * @return	Array
	 */
	private function wrapInStructure($attributes=[], $id=null, $others=[])
	{
		$data = [
			"type"       => $this->getResourceType(),
			"attributes" => ($attributes == []) ? $this->getTestAttributes() : $attributes,
		];

		if ($id != null)	$data = array_merge($data, ["id" => $id]);
		if ($others != [])	$data = array_merge($data, $others);

		return ["data" => $data];
	}

	/** Must be overridden */
	protected function getResourceType()
	{
		throw new TestException("Method getResourceType should be overridden.");
	}
}

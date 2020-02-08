<?php

namespace TravelCompanion\Traits;

use TravelCompanion\Exceptions\BadRequestException;
use TravelCompanion\Exceptions\RequestStructureException;

trait RequestFormat
{
	protected function validateRequestStructureOrFail($data)
	{
		if (! isset($data["data"]) ||
			! isset($data["data"]["type"]) ||
			! isset($data["data"]["attributes"])) {
			throw new RequestStructureException("Request has wrong structure.");
		}

		if (isset($data["data"]["relationships"])) {
			$relationships = $data["data"]["relationships"];

			foreach ($relationships as $relationship) {
				if (! isset($relationship["type"]) ||
					! isset($relationship["id"]))
					throw new BadRequestException("Relationships require at least type and id.");
			}
		}
	}

	protected function validateRelationshipsPresentOrFail($data, $names)
	{
		foreach ($names as $relationship_name) {
			if (! $this->hasRelationship($data, $relationship_name))
				throw new BadRequestException("The relationship " . $relationship_name . " is required.");
		}
	}

	protected function retrieveRelationshipOrFail($data, $name)
	{
		if (! $this->hasRelationship($data, $name))
			throw new BadRequestException("The relationship " . $name . " is required.");

		return $data["data"]["relationships"][$name];
	}

	protected function retrieveRelationship($data, $name)
	{
		if (! $this->hasRelationship($data, $name))
			return null;

		return $data["data"]["relationships"][$name];
	}

	protected function hasRelationship($data, $name)
	{
		return isset($data["data"]["relationships"][$name]);
	}

	protected function validateOwnerRelationship($data, $user)
	{
		$owner = $this->retrieveRelationshipOrFail($data, "owner");

		if (! isset($owner["type"]) ||
			! isset($owner["id"]))
			throw new BadRequestException("The owner is not specified correctly.");

		if ($owner["id"] !== $user->id)
			throw new BadRequestException("The owner does not correspond to the requesting user.");
	}
}

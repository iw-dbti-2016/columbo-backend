<?php

namespace Columbo\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
	private $token;

	public function __construct($resource, $token=null)
	{
		$this->resource = $resource;
		$this->token = $token;
	}

	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			"id"                => $this->id,
			"first_name"        => $this->first_name,
			"middle_name"       => $this->middle_name,
			"last_name"         => $this->last_name,
			"username"          => $this->username,
			"email"             => $this->email,
			"email_hash"		=> md5($this->email),
			"telephone"         => $this->telephone,
			"image"             => $this->image,
			"birth_date"        => $this->birth_date,
			"description"       => $this->description,
			"language"          => $this->language,
			"token"             => $this->when($this->token != null, $this->token),
			"email_verified_at" => $this->email_verified_at,
			"role_label" => $this->whenPivotLoaded('trip_user_role_members', function() {
				return $this->pivot->role_label;
			}),
		];
	}
}

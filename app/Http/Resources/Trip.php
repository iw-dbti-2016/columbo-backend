<?php

namespace Columbo\Http\Resources;

use Columbo\Http\Resources\Report;
use Columbo\Http\Resources\User;
use Illuminate\Http\Resources\Json\JsonResource;

class Trip extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			"id"           => $this->id,
			"name"         => $this->name,
			"synopsis"     => $this->synopsis,
			"description"  => $this->description,
			"start_date"   => $this->start_date,
			"end_date"     => $this->end_date,
			"reports"      => Report::collection($this->whenLoaded('reports')),
			"published_at" => $this->published_at,
			"members"      => User::collection($this->whenLoaded('members')),
		];
	}
}

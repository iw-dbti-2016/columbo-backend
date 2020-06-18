<?php

namespace Columbo\Http\Resources;

use Columbo\Http\Resources\Section;
use Columbo\Http\Resources\Trip;
use Columbo\Http\Resources\User;
use Illuminate\Http\Resources\Json\JsonResource;

class Report extends JsonResource
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
			"title"        => $this->title,
			"date"         => $this->date,
			"description"  => $this->description,
			"published_at" => $this->published_at,
			"sections"     => Section::collection($this->whenLoaded('sections')),
			// "plan"         => new Plan($this->whenLoaded('plan')),
			"owner"        => new User($this->whenLoaded('owner')),
			"trip"         => new Trip($this->whenLoaded('trip')),
			"visibility"   => $this->visibility,
		];
	}
}

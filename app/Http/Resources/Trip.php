<?php

namespace Columbo\Http\Resources;

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
			"published_at" => $this->published_at,
		];
	}
}

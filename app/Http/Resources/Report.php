<?php

namespace TravelCompanion\Http\Resources;

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
			// "data" => [
				// "type"       => "report",
				"id"         => $this->id,
				// "attributes" => [
					"title"        => $this->title,
					"date"         => $this->date,
					"description"  => $this->description,
					"published_at" => $this->published_at,
				// ],
			// ],
			// "links" => [
				// "self" => url("/reports/" . $this->id),
			// ],
		];
	}
}

<?php

namespace TravelCompanion\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Section extends JsonResource
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
			"data" => [
				"type"       => "section",
				"id"         => $this->id,
				"attributes" => [
					"is_draft"     => $this->is_draft,
					"content"      => $this->content,
					"image"        => $this->image,
					"time"         => $this->time,
					"duration"     => $this->duration,
					"published_at" => $this->published_at,
				],
			],
			"links" => [
				"self" => url("/sections/" . $this->id),
			],
		];
	}
}
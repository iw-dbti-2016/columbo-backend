<?php

namespace Columbo\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Location extends JsonResource
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
			"is_draft"     => $this->is_draft,
			"coordinates"  => $this->coordinates,
			"map_zoom"     => $this->map_zoom,
			"name"         => $this->name,
			"info"         => $this->info,
			"published_at" => $this->published_at,
			"visibility"   => $this->visibility,
			"user_id"      => $this->user_id,
			"distance"     => $this->distance,
		];
	}
}

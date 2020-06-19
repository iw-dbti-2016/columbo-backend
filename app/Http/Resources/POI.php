<?php

namespace Columbo\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class POI extends JsonResource
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
			"id"          => $this->uuid,
			"coordinates" => $this->coordinates,
			"map_zoom"    => $this->map_zoom,
			"name"        => $this->name,
			"info"        => $this->info,
			"image"       => $this->image,
			"distance"    => $this->distance,
		];
	}
}

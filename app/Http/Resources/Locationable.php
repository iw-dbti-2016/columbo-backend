<?php

namespace Columbo\Http\Resources;

use Columbo\Http\Resources\Location;
use Columbo\Http\Resources\POI;
use Illuminate\Http\Resources\Json\JsonResource;

class Locationable extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request)
	{
		// Yes it's ugly, but extremely helpful!
		return $this->when(true, function() {
			switch (true) {
				case $this->resource instanceof \Columbo\Location:
					return ["location" => new Location($this->resource)];

				case $this->resource instanceof \Columbo\POI:
					return ["poi" => new POI($this->resource)];
			}
		});
	}
}

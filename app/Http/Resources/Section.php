<?php

namespace Columbo\Http\Resources;

use Columbo\Http\Resources\Locationable;
use Columbo\Http\Resources\Report;
use Columbo\Http\Resources\User;
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
			"id"            => $this->id,
			"is_draft"      => $this->is_draft,
			"content"       => $this->content,
			"image"         => $this->image,
			"image_caption" => $this->image_caption,
			"start_time"    => $this->start_time,
			"end_time"      => $this->end_time,
			"temperature"   => $this->temperature,
			"published_at"  => $this->published_at,
			"report"        => new Report($this->whenLoaded('report')),
			"owner"         => new User($this->whenLoaded('owner')),
			"locationable"  => new Locationable($this->whenLoaded('locationable')),
		];
	}
}

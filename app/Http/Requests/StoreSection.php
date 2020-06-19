<?php

namespace Columbo\Http\Requests;

use Columbo\Rules\Visibility;
use Columbo\Section;
use Illuminate\Foundation\Http\FormRequest;

class StoreSection extends FormRequest
{
	public function authorize()
	{
		return $this->user()->can('create', [Section::class, $this->report]);
	}

	public function rules()
	{
		return [
			"content"      => "required",
			"image"        => "nullable",
			"start_time"   => "required|date_format:H:i",
			"end_time"     => "required|date_format:H:i",
			"visibility"   => ["required", new Visibility],
			"published_at" => "nullable|date_format:Y-m-d H:i:s",

			"locationable.type" => "nullable|in:location,poi",
			"locationable.id" => "required_if:locationable.type,location,poi",
		];
	}
}

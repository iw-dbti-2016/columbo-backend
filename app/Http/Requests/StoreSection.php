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
			"content"       => "required",
			"temperature"   => "nullable|integer",
			"image_file"    => "nullable|image",
			"image_caption" => "nullable",
			"start_time"    => "required|date_format:H:i",
			"end_time"      => "required|date_format:H:i",
			"visibility"    => ["required", new Visibility],
			"published_at"  => "nullable|date_format:Y-m-d H:i:s",

			"locationables" => "nullable|array",
			"locationables.*.type" => "in:location,poi",
			"locationables.*.id" => "required|distinct",
		];
	}
}

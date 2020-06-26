<?php

namespace Columbo\Http\Requests;

use Columbo\Rules\Visibility;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSection extends FormRequest
{
	public function authorize()
	{
		return $this->user()->can('update', $this->section);
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

			"locationables" => "nullable|array",
			"locationables.*.type" => "in:location,poi",
			"locationables.*.id" => "required|distinct",
		];
	}
}

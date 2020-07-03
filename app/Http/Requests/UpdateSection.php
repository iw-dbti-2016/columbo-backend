<?php

namespace Columbo\Http\Requests;

use Columbo\Rules\ImageBase64;
use Columbo\Rules\Visibility;
use Columbo\Rules\WeatherIcon;
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
			"start_time"    => "required|date_format:H:i",
			"end_time"      => "required|date_format:H:i",
			"weather_icon"  => ["required", new WeatherIcon],
			"temperature"   => "nullable|integer",
			"remove_image"  => "required|boolean",
			"image_file"    => ["exclude_if:remove_image,true", "nullable", "max:11250000", new ImageBase64(["image/jpeg","image/png"])],
			"image_caption" => "nullable|max:100|regex:/[^<>{}=\[\]]*/",
			"content"       => ["required", "max:10000"],
			"is_draft"      => "required|boolean",
			"published_at"  => "nullable|exclude_if:is_draft,true|date_format:Y-m-d\\TH:i",
			"visibility"    => ["required", new Visibility],

			"locationables"        => "nullable|array|max:4",
			"locationables.*.type" => "required|in:location,poi",
			"locationables.*.id"   => "required|distinct",
		];
	}
}

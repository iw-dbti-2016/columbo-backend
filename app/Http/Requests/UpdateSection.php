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
			"image"        => "nullable",
			"weather_icon"  => ["required", new WeatherIcon],
			"end_time"     => "required|date_format:H:i",
			"visibility"   => ["required", new Visibility],
			"image_file"    => ["exclude_if:remove_image,true", "nullable", "max:11250000", new ImageBase64(["image/jpeg","image/png"])],
			"image_caption" => "nullable|max:100|regex:/[^<>{}=\[\]]*/",
			"content"       => ["required", "max:10000"],
			"published_at"  => "nullable|exclude_if:is_draft,true|date_format:Y-m-d\\TH:i",

			"locationables" => "nullable|array",
			"locationables.*.type" => "in:location,poi",
			"locationables.*.id" => "required|distinct",
		];
	}
}

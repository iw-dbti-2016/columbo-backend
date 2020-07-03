<?php

namespace Columbo\Http\Requests;

use Columbo\Rules\ImageBase64;
use Columbo\Rules\Visibility;
use Columbo\Rules\WeatherIcon;
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
			"start_time"    => "required|date_format:H:i",
			"end_time"      => "required|date_format:H:i",
			"weather_icon"  => ["required", new WeatherIcon],
			"image_file"    => ["nullable", "max:11250000", new ImageBase64(["image/jpeg","image/png"])],
			"image_caption" => "nullable|max:100|regex:/[^<>{}=\[\]]*/",
			"visibility"    => ["required", new Visibility],
			"published_at"  => "nullable|date_format:Y-m-d H:i:s",

			"locationables" => "nullable|array",
			"locationables.*.type" => "in:location,poi",
			"locationables.*.id" => "required|distinct",
		];
	}
}

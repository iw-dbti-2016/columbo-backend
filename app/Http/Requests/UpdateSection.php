<?php

namespace Columbo\Http\Requests;

use Columbo\Rules\Visibility;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSection extends FormRequest
{
	public function authorize()
	{
		return $this->authorize('update', $this->section);
	}

	public function rules()
	{
		return [
			"content"          => "required",
			"image"            => "nullable",
			"time"             => "nullable|date_format:H:i",
			"duration_minutes" => "nullable|integer",
			"visibility"       => ["required", new Visibility],
			"published_at"     => "nullable|date_format:Y-m-d H:i:s",
		];
	}
}

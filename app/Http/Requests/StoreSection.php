<?php

namespace Columbo\Http\Requests;

use Columbo\Rules\Visibility;
use Columbo\Section;
use Illuminate\Foundation\Http\FormRequest;

class StoreSection extends FormRequest
{
	public function authorize()
	{
		return $this->authorize('create', [Section::class, $this->report]);
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

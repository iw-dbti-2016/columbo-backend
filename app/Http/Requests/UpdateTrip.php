<?php

namespace Columbo\Http\Requests;

use Columbo\Rules\Visibility;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTrip extends FormRequest
{
	public function authorize()
	{
		return $this->user()->can('update', $this->trip);
	}

	public function rules()
	{
		return [
			"name"         => "required|max:100",
			"synopsis"     => "nullable|max:100",
			"description"  => "nullable",
			"start_date"   => "required|date_format:Y-m-d",
			"end_date"     => "required|date_format:Y-m-d",
			"visibility"   => ["required", new Visibility],
			"published_at" => "nullable|date_format:Y-m-d H:i:s",
		];
	}
}

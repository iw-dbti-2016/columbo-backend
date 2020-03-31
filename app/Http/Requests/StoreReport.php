<?php

namespace Columbo\Http\Requests;

use Columbo\Report;
use Columbo\Rules\Visibility;
use Illuminate\Foundation\Http\FormRequest;

class StoreReport extends FormRequest
{
	public function authorize()
	{
		return $this->user()->can('create', [Report::class, $this->trip]);
	}

	public function rules()
	{
		return [
			"title"        => "required|max:100",
			"date"         => "nullable|date_format:Y-m-d",
			"description"  => "nullable|max:5000",
			"visibility"   => ["required", new Visibility],
			"published_at" => "nullable|date_format:Y-m-d H:i:s",
		];
	}
}

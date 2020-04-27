<?php

namespace Columbo\Http\Requests;

use Columbo\Rules\Visibility;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLocation extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return $this->user()->can('update', $this->location);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			"is_draft"     => "required|boolean",
			"coordinates"  => "required|array|size:2",
			"map_zoom"     => "required|numeric|min:0",
			"name"         => "required|max:100",
			"info"         => "nullable",
			"visibility"   => ["required", new Visibility],
			"published_at" => "nullable|date_format:Y-m-d H:i:s",
		];
	}
}

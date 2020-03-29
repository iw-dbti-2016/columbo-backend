<?php

namespace Columbo\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Str;

class ActionType implements CastsAttributes
{
	public function get($model, $key, $value, $attributes)
	{
		return array_search($value, config("mapping.action"));
	}

	public function set($model, $key, $value, $attributes)
	{
		return config("mapping.action")[Str::slug($value)];
	}
}

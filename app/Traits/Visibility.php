<?php

namespace Columbo\Traits;

use Illuminate\Support\Str;

trait Visibility
{
	protected function setVisibilityAttribute($value)
	{
		$this->attributes["visibility"] = config("mapping.visibility")[Str::slug($value)];
	}

	protected function getVisibilityAttribute($value)
	{
		return array_search($value, config("mapping.visibility"));
	}

	protected function isVisibleForUser($user)
	{
		return true;
	}
}

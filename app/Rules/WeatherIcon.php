<?php

namespace Columbo\Rules;

use Illuminate\Contracts\Validation\Rule;

class WeatherIcon implements Rule
{
	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		return isset(config("mapping.weathericons")[$value]);
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute is not valid.';
	}
}

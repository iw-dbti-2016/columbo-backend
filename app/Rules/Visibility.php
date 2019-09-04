<?php

namespace TravelCompanion\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class Visibility implements Rule
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
        return isset(config("mapping.visibility")[Str::slug($value)]);
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

<?php

namespace Columbo\Rules;

use Illuminate\Contracts\Validation\Rule;

class ImageBase64 implements Rule
{
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// BASED ON https://medium.com/@jagadeshanh/image-upload-and-validation-using-laravel-and-vuejs-e71e0f094fbb //
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	private $allowedFiletypes;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($allowedFiletypes)
    {
        $this->allowedFiletypes = $allowedFiletypes;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
		$image = base64_decode(explode(',', $value)[1]);

		$f = finfo_open();
		$mimeType = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);

		if (in_array($mimeType, $this->allowedFiletypes)) {
			return true;
		}

		return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The image should be a valid base64 image of type: ' . implode(' of ', $this->allowedFiletypes);
    }
}

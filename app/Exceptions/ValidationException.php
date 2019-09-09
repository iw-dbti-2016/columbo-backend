<?php

namespace TravelCompanion\Exceptions;

use Exception;
use Illuminate\Validation\Validator;
use TravelCompanion\Traits\APIResponses;

class ValidationException extends Exception
{
    use APIResponses;

    private $validator;

    function __construct(Validator $validator)
    {
        parent::__construct('The given data was invalid.');

        $this->validator = $validator;
    }
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return $this->validationFailedResponse($this->validator);
    }
}

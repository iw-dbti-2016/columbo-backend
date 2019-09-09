<?php

namespace TravelCompanion\Exceptions;

use Exception;
use TravelCompanion\Traits\APIResponses;

class ResourceNotFoundException extends Exception
{
    use APIResponses;

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return $this->resourceNotFoundResponse();
    }
}

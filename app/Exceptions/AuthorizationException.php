<?php

namespace Columbo\Exceptions;

use Exception;
use Columbo\Traits\APIResponses;

class AuthorizationException extends Exception
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
        return $this->unauthorizedResponse();
    }
}

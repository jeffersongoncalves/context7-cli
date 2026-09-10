<?php

namespace App\Exceptions;

use JeffersonGoncalves\LaravelZero\Credentials\AuthenticationException as BaseAuthenticationException;

class AuthenticationException extends BaseAuthenticationException
{
    public function __construct(string $message = 'No Context7 API key found. Run "context7 auth:save <api-key>" first (get one at https://context7.com/dashboard).')
    {
        parent::__construct($message);
    }
}

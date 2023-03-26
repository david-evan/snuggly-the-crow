<?php

namespace Domain\Users\Exceptions;

class BadUserCredentialsException extends \LogicException
{
    public function __construct()
    {
        parent::__construct(
            'Login failed because bad user credentials'
        );
    }
}

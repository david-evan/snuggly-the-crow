<?php

namespace Domain\Common\Exceptions;

class UnauthenticatedUserException extends \LogicException
{
    public function __construct()
    {
        parent::__construct('Current user cannot be authenticated');
    }
}

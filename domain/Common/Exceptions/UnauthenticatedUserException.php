<?php

namespace Domain\Common\Exceptions;

use LogicException;

class UnauthenticatedUserException extends LogicException
{
    public function __construct()
    {
        parent::__construct('Current user cannot be authenticated');
    }
}

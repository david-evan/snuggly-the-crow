<?php

namespace Domain\Users\Exceptions;

class UserAlreadyExistException extends \LogicException
{
    public function __construct(string $username)
    {
        parent::__construct(
            'Cannot create user because username [' . $username . '] already exist in system'
        );
    }
}

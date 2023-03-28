<?php

namespace Domain\Common\Services\Interfaces;

use Domain\Common\Exceptions\UnauthenticatedUserException;
use Domain\Users\Entities\User;

interface AuthenticationService
{
    /**
     * @param string $apiKey
     * @return bool
     */
    public function authenticateFromAPIKey(string $apiKey): bool;

    /**
     * @return User|null
     */
    public function getAuthenticatedUser(): ?User;

    /**
     * @return User
     * @throws UnauthenticatedUserException
     */
    public function getAuthenticatedUserOrFail(): User;

    /**
     * @return bool
     */
    public function isAuthenticated(): bool;

    /**
     * @return void
     * @throws UnauthenticatedUserException
     */
    public function mustBeAuthenticatedOrFail(): void;
}

<?php

namespace Domain\Common\Services\Implementations;

use Domain\Common\Exceptions\UnauthenticatedUserException;
use Domain\Common\Services\Interfaces\AuthenticationService;
use Domain\Users\Entities\User;
use Domain\Users\Services\Interfaces\UserService;

class AuthenticationServiceImpl implements AuthenticationService
{
    protected ?User $authenticatedUser = null;

    public function __construct(
        protected UserService $userService,
    )
    {
    }

    public function authenticateFromAPIKey(string $apiKey): bool
    {
        $user = $this->getCurrentUserFromApiKey($apiKey);
        if ($user instanceof User && now()->lessThanOrEqualTo($user->api_key_expire_at)) {
            $this->authenticatedUser = $user;
        }
        return $this->authenticatedUser instanceof User;
    }

    protected function getCurrentUserFromApiKey(string $apiKey): ?User
    {
        return $this->userService->findUserFromAPIKey($apiKey);
    }

    public function getAuthenticatedUser(): ?User
    {
        return $this->authenticatedUser;
    }

    public function getAuthenticatedUserOrFail(): User
    {
        return $this->authenticatedUser
            ?? throw new UnauthenticatedUserException();
    }

    public function mustBeAuthenticatedOrFail(): void
    {
        if (!$this->isAuthenticated()) {
            throw new UnauthenticatedUserException();
        }
    }

    public function isAuthenticated(): bool
    {
        return $this->authenticatedUser instanceof User;
    }

}

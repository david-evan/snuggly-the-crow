<?php

namespace Domain\Users\Services\Interfaces;

use Domain\Users\Entities\User;
use Domain\Users\Exceptions\BadUserCredentialsException;
use Domain\Users\Exceptions\UserAlreadyExistException;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

interface UserService
{
    /**
     * @param string $username
     * @param string $presentedPassword
     * @return User
     * @throws BadUserCredentialsException
     */
    public function login(string $username, string $presentedPassword): User;

    /**
     * @param string $username
     * @param string $password
     * @return User
     * @throws UserAlreadyExistException
     * @throws InvalidArgumentException
     */
    public function createUser(string $username, string $password): User;

    /**
     * @return Collection
     */
    public function findAll(): Collection;

    /**
     * @param User $user
     * @return void
     */
    public function delete(User $user): void;

    /**
     * @param string $apiKey
     * @return User|null
     */
    public function findUserFromAPIKey(string $apiKey): ?User;
}

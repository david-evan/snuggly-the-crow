<?php

namespace Domain\Users\Services\Implementations;

use Domain\Users\Entities\User;
use Domain\Users\Exceptions\BadUserCredentialsException;
use Domain\Users\Exceptions\UserAlreadyExistException;
use Domain\Users\Services\Interfaces\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class UserServiceImpl implements UserService
{

    public function findAll(): Collection
    {
        return User::all();
    }

    public function login(string $username, string $presentedPassword): User
    {
        try {
            /** @var User $user */
            $user = User::whereUsernameIs($username)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new BadUserCredentialsException();
        }

        if ($user->password !== $user->getHashedPassword($presentedPassword)) {
            throw new BadUserCredentialsException();
        }

        $user->updateLastLoginAndGenerateNewApiKey()->save();
        return $user;
    }

    public function createUser(string $username, string $password): User
    {
        $user = new User([
            'username' => $username,
            'password' => $password,
        ]);
        try {
            $user->updateLastLoginAndGenerateNewApiKey()->save();
        } catch (QueryException $exception) {
            throw new UserAlreadyExistException($username);
        }

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}

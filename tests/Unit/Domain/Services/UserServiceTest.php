<?php

namespace Tests\Unit\Domain\Services;

use Domain\Users\Entities\User;
use Domain\Users\Exceptions\BadUserCredentialsException;
use Domain\Users\Exceptions\UserAlreadyExistException;
use Domain\Users\Services\Interfaces\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class UserServiceTest  extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    public function test_findAllUser(): void
    {
        $this->userService = $this->app->make(UserService::class);
        // given ...
        $nbUserToCreate = 10;
        User::factory()->count($nbUserToCreate)->create();

        // then ...
        $this->assertTrue(
            $nbUserToCreate === count(
                // when ...
                $this->userService->findAll()
            ));
    }

    public function test_successLogin(): void
    {
        $this->userService = $this->app->make(UserService::class);

        // given ...
        /** @var User $user */
        $user =  User::factory()->make();
        $password = fake()->password;
        $user->password = $password;
        $user->save();

        // when ...
        $loggedUser = $this->userService->login($user->username, $password);

        // then ...
        $this->assertTrue($user->id === $loggedUser->id);
        $this->assertTrue(Uuid::isValid($loggedUser->api_key));
        $this->assertTrue(
            now()->addMinutes(User::API_KEY_VALIDITY_MINUTES)->format(DATE_RFC3339)
            === $loggedUser->api_key_expire_at
        );
    }

    public function test_failLoginBecauseBadUsername(): void
    {
        $this->userService = $this->app->make(UserService::class);

        // given ...
        /** @var User $user */
        $user =  User::factory()->make();
        $password = fake()->password;
        $user->password = $password;
        $user->save();

        // then ...
        $this->expectException(BadUserCredentialsException::class);

        // when ...
        $this->userService->login(fake()->userName, $password);
    }

    public function test_failLoginBecauseBadPassword(): void
    {
        $this->userService = $this->app->make(UserService::class);

        // given ...
        /** @var User $user */
        $user =  User::factory()->create();

        // then ...
        $this->expectException(BadUserCredentialsException::class);

        // when ...
        $this->userService->login($user->username, fake()->password);
    }

    public function test_successCreateUser(): void
    {
        $this->userService = $this->app->make(UserService::class);

        // given ...
        $username = fake()->userName;
        $password = fake()->password;

        // when ...
        $user = $this->userService->createUser($username, $password);

        // then ...
        $this->assertTrue($user->username == $username);
        $this->assertTrue($user->password === User::getHashedPassword($password));
        $this->assertTrue(Uuid::isValid($user->api_key));
        $this->assertTrue(
            now()->addMinutes(User::API_KEY_VALIDITY_MINUTES)->format(DATE_RFC3339)
            === $user->api_key_expire_at
        );
    }

    public function test_failCreateUserBecauseUserAlreadyExist(): void
    {
        $this->userService = $this->app->make(UserService::class);

        // given ...
        /** @var User $existingUser */
        $existingUser =  User::factory()->create();
        // then ...
        $this->expectException(UserAlreadyExistException::class);
        // when ...
        $this->userService->createUser($existingUser->username, fake()->password);
    }

    public function test_successDeleteUser(): void
    {
        $this->userService = $this->app->make(UserService::class);

        // given ...
        /** @var User $user */
        $user =  User::factory()->create();
        // when ...
        $this->userService->delete($user);
        // then ...
        $this->assertNull(User::find($user->id));
    }
}

<?php

namespace Tests\Unit\Domain\Entities;

use Domain\Users\Entities\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_usernameValidationConstraints(): void
    {
        // given ...
        $user = new User();
        // then ...
        $this->expectException(\InvalidArgumentException::class);
        // when ...
        $user->username = Str::random(User::USERNAME_MAX_LENGTH+1);
        $user->username = Str::random(User::USERNAME_MIN_LENGTH-1);
    }

    public function test_passwordValidationConstraints(): void
    {
        // given ...
        $user = new User();
        // then ...
        $this->expectException(\InvalidArgumentException::class);
        // when ...
        $user->password = Str::random(User::PASSWORD_MAX_LENGTH+1);
        $user->password = Str::random(User::PASSWORD_MIN_LENGTH-1);
    }
}

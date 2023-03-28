<?php

namespace Tests\Feature\Controllers;

use App\Library\SDK\Definitions\HttpCode;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Feature\FeatureTestCase;

class UserControllerTest extends FeatureTestCase
{
    use WithoutMiddleware;

    public function test_getAllUsers(): void
    {
        // when ...
        $response = $this->get(route('users.index'));
        // then ...
        $response->assertStatus(HttpCode::HTTP_OK);
    }

    public function test_deleteUser(): void
    {
        // given ...
        /** @var User $user */
        $user = User::factory()->create();

        // when ...
        $response = $this->delete(route('users.destroy', [
            'user' => $user->id
        ]));

        // then ...
        $response->assertStatus(HttpCode::HTTP_NO_CONTENT);
    }

    public function test_createUser(): void
    {
        // when ...
        $response = $this->postJson(route('users.store'), [
            'username' => fake()->userName,
            'password' => fake()->password,
        ]);

        // then ...
        $response->assertStatus(HttpCode::HTTP_CREATED);
    }

    public function test_login(): void
    {
        /** @var User $user */
        $user =  User::factory()->make();
        $password = fake()->password;
        $user->password = $password;
        $user->save();

        // when ...
        $response = $this->postJson(route('users.login'), [
            'username' => $user->username,
            'password' => $password,
        ]);

        // then ...
        $response->assertStatus(HttpCode::HTTP_OK);
    }

}

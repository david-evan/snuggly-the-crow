<?php

namespace Application\Resources\Users;

use App\Modules\Users\Resources\JSON\AuthenticatedUserResource;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedUserResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_AuthenticatedUserResourceStructure()
    {
        // given ...
        /** @var User $user */
        $user = User::factory()->make();
        $user->updateLastLoginAndGenerateNewApiKey();
        $user->save();

        // when ...
        $userJsonArray = json_decode((new AuthenticatedUserResource($user))->toJson(), true);

        // then ...
        $this->assertArrayHasKey('id', $userJsonArray);
        $this->assertArrayHasKey('username', $userJsonArray);
        $this->assertArrayHasKey('last_login', $userJsonArray);
        $this->assertArrayHasKey('created_at', $userJsonArray);
        $this->assertArrayHasKey('api_key', $userJsonArray);
        $this->assertArrayHasKey('api_key_expire_at', $userJsonArray);

        $this->assertTrue(6 === count($userJsonArray));
    }
}

<?php

namespace Application\Resources\Users;

use App\Modules\Users\Resources\JSON\UserResource;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_UserResourceStructure()
    {
        // given ...
        $user = User::factory()->create();

        // when ...
        $userJsonArray = json_decode((new UserResource($user))->toJson(), true);

        // then ...
        $this->assertArrayHasKey('id', $userJsonArray);
        $this->assertArrayHasKey('username', $userJsonArray);
        $this->assertArrayHasKey('last_login', $userJsonArray);
        $this->assertArrayHasKey('created_at', $userJsonArray);

        $this->assertTrue(4 === count($userJsonArray));
    }
}

<?php

namespace Application\Resources\Users;

use App\Modules\Users\Resources\JSON\UserResource;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class UserResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_UserResourceStructure()
    {
        // given ...

        /** @var User $user */
        $user = User::factory()->make();
        $user->updateLastLoginAndGenerateNewApiKey()->save();

        // when ...
        $userJsonArray = json_decode((new UserResource($user))->toJson(), true);

        // then ...
        $this->assertArrayHasKey('id', $userJsonArray);
        $this->assertArrayHasKey('username', $userJsonArray);
        $this->assertArrayHasKey('last_login', $userJsonArray);
        $this->assertArrayHasKey('created_at', $userJsonArray);

        $this->assertTrue(4 === count($userJsonArray));

        $this->assertTrue(Uuid::isValid($userJsonArray['id'] ?? null ));
        $this->assertTrue(Carbon::createFromFormat(DATE_RFC3339, $userJsonArray['last_login'] ?? null) instanceof \DateTime);
        $this->assertTrue(Carbon::createFromFormat(DATE_RFC3339, $userJsonArray['created_at'] ?? null) instanceof \DateTime);
    }
}

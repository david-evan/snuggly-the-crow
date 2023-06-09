<?php

namespace Tests\Unit\Application\Resources\Users;

use App\Modules\Users\Resources\JSON\AuthenticatedUserResource;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class AuthenticatedUserResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_AuthenticatedUserResourceStructure(): void
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

        $this->assertTrue(Uuid::isValid($userJsonArray['id'] ?? null ));
        $this->assertTrue(Uuid::isValid($userJsonArray['api_key'] ?? null ));

        $this->assertTrue(Carbon::createFromFormat(DATE_RFC3339, $userJsonArray['api_key_expire_at'] ?? null) instanceof \DateTime);
        $this->assertTrue(Carbon::createFromFormat(DATE_RFC3339, $userJsonArray['last_login'] ?? null) instanceof \DateTime);
        $this->assertTrue(Carbon::createFromFormat(DATE_RFC3339, $userJsonArray['created_at'] ?? null) instanceof \DateTime);
    }
}

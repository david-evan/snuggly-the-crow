<?php

namespace Application\Resources\Blog;

use App\Modules\Blog\Resources\JSON\AuthorResource;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_AuthorResourceStructure()
    {
        // given ...
        /** @var User $user */
        $user = User::factory()->create();

        // when ...
        $userJsonArray = json_decode((new AuthorResource($user))->toJson(), true);

        // then ...
        $this->assertArrayHasKey('id', $userJsonArray);
        $this->assertArrayHasKey('username', $userJsonArray);
        $this->assertTrue(2 === count($userJsonArray));
    }

}

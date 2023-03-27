<?php

namespace Application\Resources\Blog;

use App\Modules\Blog\Resources\JSON\AuthorResource;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
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
        $authorJsonArray = json_decode((new AuthorResource($user))->toJson(), true);

        // then ...
        $this->assertArrayHasKey('id', $authorJsonArray);
        $this->assertArrayHasKey('username', $authorJsonArray);

        $this->assertTrue(2 === count($authorJsonArray));

        $this->assertTrue(Uuid::isValid($authorJsonArray['id'] ?? null ));
    }

}

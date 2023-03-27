<?php

namespace Application\Resources\Blog;

use App\Modules\Blog\Resources\JSON\ArticleResource;
use Domain\Blog\Entities\Article;
use Domain\Blog\ValueObjects\Status;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ArticleResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_ArticleResourceStructure()
    {
        // given ...
        /** @var User $user */
        $user = User::factory()->create();
        $article = $user->articles()->save(
            Article::factory()->draft()->publishAtRandomDate()->make()
        )->refresh();

        // when ...
        $articleJsonArray = json_decode((new ArticleResource($article))->toJson(), true);

        // then ...
        $this->assertArrayHasKey('id', $articleJsonArray);
        $this->assertArrayHasKey('title', $articleJsonArray);
        $this->assertArrayHasKey('content', $articleJsonArray);
        $this->assertArrayHasKey('status', $articleJsonArray);
        $this->assertArrayHasKey('published_at', $articleJsonArray);
        $this->assertArrayHasKey('author', $articleJsonArray);
        $this->assertTrue(6 === count($articleJsonArray));

        $this->assertTrue(Uuid::isValid($articleJsonArray['id'] ?? null ));
        $this->assertTrue(Carbon::createFromFormat(DATE_RFC3339, $articleJsonArray['published_at'] ?? null) instanceof \DateTime);
        $this->assertTrue(null !== Status::tryFrom($articleJsonArray['status']));
    }
}

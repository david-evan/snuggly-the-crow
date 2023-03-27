<?php

namespace Tests\Unit\Application\Resources\Blog;

use App\Modules\Blog\Resources\JSON\TrashedArticleResource;
use Domain\Blog\Entities\Article;
use Domain\Blog\ValueObjects\Status;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class TrashedArticleResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_TrashedArticleResourceStructure(): void
    {
        // given ...
        /** @var User $user */
        $user = User::factory()->create();
        $article = $user->articles()->save(
            Article::factory()->trashed()->published()->make()
        )->refresh();

        // when ...
        $articleJsonArray = json_decode((new TrashedArticleResource($article))->toJson(), true);

        // then ...
        $this->assertArrayHasKey('id', $articleJsonArray);
        $this->assertArrayHasKey('title', $articleJsonArray);
        $this->assertArrayHasKey('content', $articleJsonArray);
        $this->assertArrayHasKey('status', $articleJsonArray);
        $this->assertArrayHasKey('published_at', $articleJsonArray);
        $this->assertArrayHasKey('author', $articleJsonArray);
        $this->assertArrayHasKey('created_at', $articleJsonArray);
        $this->assertArrayHasKey('updated_at', $articleJsonArray);
        $this->assertArrayHasKey('deleted_at', $articleJsonArray);
        $this->assertTrue(9 === count($articleJsonArray));

        $this->assertTrue(Uuid::isValid($articleJsonArray['id'] ?? null ));
        $this->assertTrue(Carbon::createFromFormat(DATE_RFC3339, $articleJsonArray['published_at'] ?? null) instanceof \DateTime);
        $this->assertTrue(Carbon::createFromFormat(DATE_RFC3339, $articleJsonArray['created_at'] ?? null) instanceof \DateTime);
        $this->assertTrue(Carbon::createFromFormat(DATE_RFC3339, $articleJsonArray['updated_at'] ?? null) instanceof \DateTime);
        $this->assertTrue(Carbon::createFromFormat(DATE_RFC3339, $articleJsonArray['deleted_at'] ?? null) instanceof \DateTime);
        $this->assertTrue(null !== Status::tryFrom($articleJsonArray['status']));
    }
}

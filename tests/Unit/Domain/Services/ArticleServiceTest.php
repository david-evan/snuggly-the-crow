<?php

namespace Domain\Services;

use Carbon\Carbon;
use Domain\Blog\Entities\Article;
use Domain\Blog\Exceptions\ArticleAlreadyDraftException;
use Domain\Blog\Exceptions\ArticleAlreadyPublishedException;
use Domain\Blog\Exceptions\CannotUpdatePublishedArticleException;
use Domain\Blog\Services\Interfaces\ArticleService;
use Domain\Blog\ValueObjects\Status;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleServiceTest extends TestCase
{
    use RefreshDatabase;

    private ArticleService $articleService;

    public function test_createArticleForUser(): void
    {
        $this->articleService = $this->app->make(ArticleService::class);

        // given ...
        $user = User::factory()->create();
        $article = Article::factory()->draft()->make();

        // when ...
        $createdArticle = $this->articleService->createArticleForUser($article, $user);

        // then ...
        $this->assertTrue($createdArticle->user()->first()->id ===  $user->id);
        $this->assertTrue($createdArticle->wasRecentlyCreated);
        $this->assertTrue($createdArticle->isDraft());
    }

    public function test_deleteUser(): void
    {
        $this->articleService = $this->app->make(ArticleService::class);

        // given ...
        /** @var Article $article */
        $article = User::factory()->create()->articles()
            ->save(Article::factory()->published()->make()
        );

        // when ...
        $this->articleService->delete($article);

        // then ...
        $this->assertTrue(null ===Article::find($article->id));

        $trashedArticle = Article::withTrashed()->find($article->id);
        $this->assertTrue($trashedArticle instanceof Article);
        $this->assertTrue($trashedArticle->id === $article->id);
    }

    public function test_successPublishArticleAndUpdateDate(): void
    {
        $this->articleService = $this->app->make(ArticleService::class);

        // given ...
        /** @var Article $draftArticle */
        $draftArticle = User::factory()->create()->articles()
            ->save(Article::factory()->draft()->make()
            );

        // when ...
        $publishedArticle = $this->articleService->publish($draftArticle)->refresh();

        // then ...
        $this->assertTrue($publishedArticle->isPublished());
        $this->assertTrue(now()->format(DATE_RFC3339) === $publishedArticle->published_at );
    }

    public function test_failPublishArticleBecauseAlreadyPublished(): void
    {
        $this->articleService = $this->app->make(ArticleService::class);

        // given ...
        /** @var Article $publishedArticle */
        $publishedArticle = User::factory()->create()->articles()
            ->save(Article::factory()->published()->make()
            );

        // then ...
        $this->expectException(ArticleAlreadyPublishedException::class);

        // when ...
         $this->articleService->publish($publishedArticle);
    }

    public function test_successDraftArticle(): void
    {
        $this->articleService = $this->app->make(ArticleService::class);

        // given ...
        /** @var Article $publishedArticle */
        $publishedArticle = User::factory()->create()->articles()
            ->save(Article::factory()->published()->make()
            );

        // when ...
        $publishedArticle = $this->articleService->draft($publishedArticle)->refresh();

        // then ...
        $this->assertTrue($publishedArticle->isDraft());
    }

    public function test_failDraftArticleBecauseAlreadyDraft(): void
    {
        $this->articleService = $this->app->make(ArticleService::class);

        // given ...
        /** @var Article $draftArticle */
        $draftArticle = User::factory()->create()->articles()
            ->save(Article::factory()->draft()->make()
            );

        // then ...
        $this->expectException(ArticleAlreadyDraftException::class);

        // when ...
        $this->articleService->draft($draftArticle);
    }

    public function test_failUpdatePublishedArticleWhenStatusIsNotDraft(): void
    {
        $this->articleService = $this->app->make(ArticleService::class);

        // given ...
        /** @var Article $publishedArticle */
        $publishedArticle = User::factory()->create()->articles()
            ->save(Article::factory()->published()->make()
            );
        /** @var Article $futurArticle */
        $futurArticle = Article::factory()->published()->make();

        // then ...
        $this->expectException(CannotUpdatePublishedArticleException::class);

        // when ...
        $this->articleService->updateArticle($futurArticle, $publishedArticle);
    }

    public function test_successUpdateDraftArticle(): void
    {
        $this->articleService = $this->app->make(ArticleService::class);

        // given ...
        /** @var Article $articleToUpdate */
        $articleToUpdate = User::factory()->create()->articles()
            ->save(Article::factory()->draft()->make()
            );
        /** @var Article $futurArticle */
        $futurArticle = Article::factory()->published()->make();

        // when ...
        $articleToUpdate = $this->articleService->updateArticle($futurArticle, $articleToUpdate);

        // then ...
        $this->assertTrue($articleToUpdate->title == $futurArticle->title);
        $this->assertTrue($articleToUpdate->content == $futurArticle->content);
        $this->assertTrue($articleToUpdate->status == $futurArticle->status);
        $this->assertTrue($articleToUpdate->published_at == $futurArticle->published_at);
    }
}

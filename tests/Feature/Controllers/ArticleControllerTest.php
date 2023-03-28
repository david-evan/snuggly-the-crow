<?php

namespace Tests\Feature\Controllers;

use App\Library\SDK\Definitions\HttpCode;
use Domain\Blog\Entities\Article;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Feature\FeatureTestCase;

class ArticleControllerTest extends FeatureTestCase
{
    use WithoutMiddleware;

    public function test_getAllArticles(): void
    {
        // when ...
        $response = $this->get(route('articles.index', [
            'perPage' => 10,
            'status' => 'draft',
        ]));
        // then ...
        $response->assertStatus(HttpCode::HTTP_OK);
    }

    public function test_getOneArticle(): void
    {
        // given ...
        /** @var Article $article */
        $article = User::factory()->create()->articles()
            ->save(Article::factory()->draft()->make()
            );

        // when ...
        $response = $this->get(route('articles.show', [
            'article' => $article->id
        ]));
        // then ...
        $response->assertStatus(HttpCode::HTTP_OK);
    }

    public function test_getTrashedArticles(): void
    {
        // when ...
        $response = $this->get(route('articles.trashed'));
        // then ...
        $response->assertStatus(HttpCode::HTTP_OK);
    }

    public function test_destroyArticle():void
    {
        // given ...
        /** @var Article $article */
        $article = User::factory()->create()->articles()
            ->save(Article::factory()->draft()->make()
            );

        // when ...
        $response = $this->delete(route('articles.destroy', [
            'article' => $article->id
        ]));

        // then ...
        $response->assertStatus(HttpCode::HTTP_NO_CONTENT);
    }

}

<?php

namespace Tests\Unit\Domain\Entities;

use Domain\Blog\Entities\Article;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    public function test_titleValidationConstraints(): void
    {
        // given ...
        $article = new Article();
        // then ...
        $this->expectException(\InvalidArgumentException::class);
        // when ...
        $article->title = Str::random(Article::TITLE_MAX_LENGTH+1);
        $article->title = Str::random(Article::TITLE_MIN_LENGTH-1);
    }

    public function test_statusValidationConstraints(): void
    {
        // given ...
        $article = new Article();
        // then ...
        $this->expectException(\InvalidArgumentException::class);
        // when ...
        $article->status = Str::random();
    }

    public function test_publishedAtValidationConstraint(): void
    {
        // given ...
        $article = new Article();
        // then ...
        $this->expectException(\InvalidArgumentException::class);
        // when ...
        $article->published_at = Str::random();
    }
}

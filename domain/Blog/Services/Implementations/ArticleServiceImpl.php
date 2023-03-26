<?php

namespace Domain\Blog\Services\Implementations;

use Domain\Blog\Entities\Article;
use Domain\Blog\Exceptions\ArticleAlreadyPublishedException;
use Domain\Blog\Exceptions\CannotUpdatePublishedArticleException;
use Domain\Blog\Services\Interfaces\ArticleService;
use Domain\Blog\ValueObjects\Status;
use Illuminate\Contracts\Pagination\Paginator;

class ArticleServiceImpl implements ArticleService
{
    public function saveArticle(Article $article): Article
    {
        if ($article->isPublished()) {
            $article->published_at = now();
        }

        $article->save();
        return $article;
    }

    public function findAll(int $limitPerPage, ?Status $status = null): Paginator
    {
        $articles = Article::query();

        if (!empty($status)) {
            $articles->where('status', $status);
        }
        return $articles->paginate($limitPerPage);
    }

    public function updateArticle(Article $futurArticle, Article $articleToUpdate): Article
    {
        if ($articleToUpdate->isPublished() && $futurArticle->isDraft() === false) {
            throw new CannotUpdatePublishedArticleException($articleToUpdate);
        }

        $articleToUpdate->title = $futurArticle->title ?? $articleToUpdate->title;
        $articleToUpdate->content = $futurArticle->content ?? $articleToUpdate->content;
        $articleToUpdate->status = $futurArticle->status ?? $articleToUpdate->status;
        $articleToUpdate->published_at = $futurArticle->published_at ?? $articleToUpdate->published_at;

        $articleToUpdate->save();

        return $articleToUpdate;
    }

    public function delete(Article $article): void
    {
        $article->delete();
    }

    public function publish(Article $article): Article
    {
        if ($article->isPublished()) {
            throw new ArticleAlreadyPublishedException($article);
        }

        $article->status = Status::PUBLISHED;
        $article->save();
        return $article;
    }

    public function draft(Article $article): Article
    {
        if ($article->isDraft()) {
            throw new ArticleAlreadyPublishedException($article);
        }

        $article->status = Status::DRAFT;
        $article->save();
        return $article;
    }
}

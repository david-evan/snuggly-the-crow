<?php

namespace Domain\Blog\Services\Implementations;

use Domain\Blog\Entities\Article;
use Domain\Blog\Exceptions\ArticleAlreadyDraftException;
use Domain\Blog\Exceptions\ArticleAlreadyPublishedException;
use Domain\Blog\Exceptions\CannotUpdatePublishedArticleException;
use Domain\Blog\Services\Interfaces\ArticleService;
use Domain\Blog\ValueObjects\Status;
use Domain\Users\Entities\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class ArticleServiceImpl implements ArticleService
{
    public function createArticleForUser(Article $article, User $user): Article
    {
        if ($article->isPublished()) {
            $article->published_at = now();
        }

        $user->articles()->save($article);
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

        if ($futurArticle->published_at instanceof \DateTime) {
            $articleToUpdate->published_at = $futurArticle->published_at;
        }

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
        $article->published_at = now();
        $article->save();
        return $article;
    }

    public function draft(Article $article): Article
    {
        if ($article->isDraft()) {
            throw new ArticleAlreadyDraftException($article);
        }

        $article->status = Status::DRAFT;
        $article->save();
        return $article;
    }

    public function findAllTrashed(): Collection
    {
        return Article::onlyTrashed()->get();
    }
}

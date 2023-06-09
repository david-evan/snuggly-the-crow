<?php

namespace Domain\Blog\Services\Interfaces;

use Domain\Blog\Entities\Article;
use Domain\Blog\Exceptions\ArticleAlreadyPublishedException;
use Domain\Blog\Exceptions\CannotUpdatePublishedArticleException;
use Domain\Blog\ValueObjects\Status;
use Domain\Users\Entities\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

interface ArticleService
{
    /**
     * @param Article $article
     * @param User $user
     * @return Article
     * @throws InvalidArgumentException
     */
    public function createArticleForUser(Article $article, User $user): Article;

    /**
     * @param int $limitPerPage - Nombre d'articles à retourner par page
     * @param Status|null $status
     * @return Paginator
     */
    public function findAll(int $limitPerPage, ?Status $status): Paginator;

    /**
     * @param Article $futurArticle
     * @param Article $articleToUpdate
     * @return Article
     * @throws  InvalidArgumentException
     * @throws CannotUpdatePublishedArticleException
     */
    public function updateArticle(Article $futurArticle, Article $articleToUpdate): Article;

    /**
     * @param Article $article
     * @return void
     */
    public function delete(Article $article): void;

    /**
     * @param Article $article
     * @return mixed
     * @throws  ArticleAlreadyPublishedException
     */
    public function publish(Article $article): Article;

    /**
     * @return Collection
     */
    public function findAllTrashed(): Collection;

}

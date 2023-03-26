<?php

namespace Domain\Blog\Services\Interfaces;

use Domain\Blog\Entities\Article;
use Domain\Blog\Exceptions\ArticleAlreadyPublishedException;
use Domain\Blog\Exceptions\CannotUpdatePublishedArticleException;
use Illuminate\Database\Eloquent\Collection;

interface ArticleService
{
    /**
     * @param Article $article
     * @return Article
     * @throws  \InvalidArgumentException
     */
    public function saveArticle(Article $article): Article;

    /**
     * @return Collection
     */
    public function findAll(): Collection;

    /**
     * @param Article $futurArticle
     * @param Article $articleToUpdate
     * @return Article
     * @throws  \InvalidArgumentException
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
}
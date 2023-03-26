<?php

namespace Domain\Blog\Exceptions;

use Domain\Blog\Entities\Article;
use Domain\Blog\ValueObjects\Status;

class ArticleAlreadyPublishedException extends \LogicException
{
    public function __construct(Article $article)
    {
        parent::__construct(
            'Article ' . $article->id . ' can not be published because its already published'
        );
    }
}

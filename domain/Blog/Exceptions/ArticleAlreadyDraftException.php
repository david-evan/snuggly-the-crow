<?php

namespace Domain\Blog\Exceptions;

use Domain\Blog\Entities\Article;

class ArticleAlreadyDraftException extends \LogicException
{
    public function __construct(Article $article)
    {
        parent::__construct(
            'Article ' . $article->id . '. can not be set as draft because its already a draft'
        );
    }
}

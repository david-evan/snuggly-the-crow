<?php

namespace App\Modules\Blog\Resources\JSON;

use Domain\Blog\Entities\Article;
use Illuminate\Http\Request;

class TrashedArticleResource extends ArticleResource
{
    public function toArray(Request $request)
    {
        /** @var Article $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'author' => new AuthorResource($this->user()->first()),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

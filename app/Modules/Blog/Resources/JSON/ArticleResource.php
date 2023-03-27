<?php

namespace App\Modules\Blog\Resources\JSON;

use Domain\Blog\Entities\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request)
    {
        /** @var Article $this */
        return [
            'id' => $this->id,
            'title' =>$this->title,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'author' => new AuthorResource($this->user()->first())
        ];
    }
}

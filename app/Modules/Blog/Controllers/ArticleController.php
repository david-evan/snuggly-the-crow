<?php

namespace App\Modules\Blog\Controllers;

use App\Library\APIProblem\Exceptions\Problems\BadRequestException;
use App\Library\SDK\Definitions\HttpCode;
use App\Modules\Blog\Requests\StoreArticleRequest;
use App\Modules\Blog\Requests\UpdateArticleRequest;
use App\Modules\Blog\Resources\JSON\ArticleResource;
use App\Modules\Common\Controllers\BaseAPIController;
use Domain\Blog\Entities\Article;
use Domain\Blog\Services\Interfaces\ArticleService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ArticleController extends BaseAPIController
{
    public function __construct(
        protected ArticleService $articleService
    ) {}

    /**
     * @GET : articles/
     * Retourne la liste des articles
     */
    public function index() : AnonymousResourceCollection
    {
        return ArticleResource::collection($this->articleService->findAll());
    }

    /**
     * @GET : /articles/{article}
     * Retourne un article (à partir de son id)
     *
     * @param Article $article
     * @return ArticleResource
     */
    public function show(Article $article) : ArticleResource
    {
        return new ArticleResource($article);
    }

    /**
     * @POST : /articles
     * Créé un article. Retourne l'article créé.
     *
     * @param StoreArticleRequest $request
     * @return ArticleResource
     * @throws BadRequestException
     */
    public function store(StoreArticleRequest $request) : ArticleResource
    {
        try {
            $article = new Article($request->validated());
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

        return new ArticleResource(
            $this->articleService->saveArticle($article)
        );
    }

    /**
     * @PUT @PATCH : /articles/{article}
     * Créé un article. Retourne l'article créé.
     *
     * @param UpdateArticleRequest $request
     * @param Article $article
     * @return ArticleResource
     * @throws BadRequestException
     */
    public function update(UpdateArticleRequest $request, Article $article) : ArticleResource
    {
        try {
            $futurArticle = new Article($request->validated());
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

        try {
            return new ArticleResource(
                $this->articleService->updateArticle($futurArticle, $article)
            );
        } catch (\LogicException $exception) {
            throw new BadRequestException($exception->getMessage());
        }
    }

    /**
     * @DELETE /articles/{article}
     * Supprime un article.
     * @param Article $article
     * @return Response
     */
    public function destroy(Article $article): Response
    {
        $this->articleService->delete($article);
        return response(null, HttpCode::HTTP_NO_CONTENT->value);
    }

    /**
     * @PUT /articles/{article}/publish
     * Publie un article
     * @param Article $article
     * @return Response
     */


    /**
     * @PUT /articles/{article}/publish
     * Publie un article
     * @param Article $article
     * @return ArticleResource
     * @throws BadRequestException
     */
    public function publish(Article $article) : ArticleResource
    {
        try {
            return new ArticleResource(
                $this->articleService->publish($article)
            );
        } catch (\LogicException $exception) {
            throw new BadRequestException($exception->getMessage());
        }
    }

    /**
     * @PUT /articles/{article}/draft
     * Passe un article au status brouillon
     * @param Article $article
     * @return ArticleResource
     * @throws BadRequestException
     */
    public function draft(Article $article) : ArticleResource
    {
        try {
            return new ArticleResource(
                $this->articleService->draft($article)
            );
        } catch (\LogicException $exception) {
            throw new BadRequestException($exception->getMessage());
        }
    }
}

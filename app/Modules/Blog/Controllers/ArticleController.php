<?php

namespace App\Modules\Blog\Controllers;

use App\Library\APIProblem\Exceptions\Problems\BadRequestException;
use App\Library\SDK\Definitions\HttpCode;
use App\Modules\Blog\Requests\GetArticles;
use App\Modules\Blog\Requests\StoreArticleRequest;
use App\Modules\Blog\Requests\UpdateArticleRequest;
use App\Modules\Blog\Resources\JSON\ArticleResource;
use App\Modules\Blog\Resources\JSON\TrashedArticleResource;
use App\Modules\Common\Controllers\BaseAPIController;
use Domain\Blog\Entities\Article;
use Domain\Blog\Services\Interfaces\ArticleService;
use Domain\Blog\ValueObjects\Status;
use Domain\Common\Services\Interfaces\AuthenticationService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ArticleController extends BaseAPIController
{
    // Constantes liées à la pagination des résultats
    private const MAX_RESULT_PER_PAGE = 100;
    private const DEFAULT_PER_PAGE_RESULT = 20;

    public function __construct(
        protected ArticleService $articleService,
        protected AuthenticationService $authenticationService
    ){}

    /**
     * @GET : /articles
     * Retourne la liste des articles
     */
    public function index(GetArticles $request): AnonymousResourceCollection
    {
        $size = $request->validated()['perPage'] ?? null;
        $status = $request->validated()['status'] ?? null;

        $articlesPerPage = (int)min($size ?? self::DEFAULT_PER_PAGE_RESULT, self::MAX_RESULT_PER_PAGE);

        $paginator = $this->articleService->findAll($articlesPerPage, Status::tryFrom($status));

        if ($size) {
            $paginator->appends('perPage', $articlesPerPage);
        }

        if ($status) {
            $paginator->appends('status', $status);
        }

        return ArticleResource::collection($paginator);
    }

    /**
     * @GET : /articles/{article}
     * Retourne un article (à partir de son id)
     *
     * @param Article $article
     * @return ArticleResource
     */
    public function show(Article $article): ArticleResource
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
    public function store(StoreArticleRequest $request): ArticleResource
    {
        try {
            $article = new Article($request->validated());
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

        $user = $this->authenticationService->getAuthenticatedUserOrFail();

        return new ArticleResource(
            $this->articleService->createArticleForUser($article, $user)
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
    public function update(UpdateArticleRequest $request, Article $article): ArticleResource
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
        return response(null, HttpCode::HTTP_NO_CONTENT);
    }

    /**
     * @PUT /articles/{article}/publish
     * Publie un article
     * @param Article $article
     * @return Response
     */


    /**
     * @PATCH /articles/{article}/publish
     * Publie un article
     * @param Article $article
     * @return ArticleResource
     * @throws BadRequestException
     */
    public function publish(Article $article): ArticleResource
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
     * @PATCH /articles/{article}/draft
     * Passe un article au status brouillon
     * @param Article $article
     * @return ArticleResource
     * @throws BadRequestException
     */
    public function draft(Article $article): ArticleResource
    {
        try {
            return new ArticleResource(
                $this->articleService->draft($article)
            );
        } catch (\LogicException $exception) {
            throw new BadRequestException($exception->getMessage());
        }
    }

    /**
     * @GET /articles/trashed
     * Renvoi la liste des articles supprimés
     */
    public function trashed(): AnonymousResourceCollection
    {
        return TrashedArticleResource::collection(
            $this->articleService->findAllTrashed()
        );
    }
}

<?php

namespace App\Modules\Users\Controllers;

use App\Library\APIProblem\Exceptions\Problems\BadRequestException;
use App\Library\SDK\Definitions\HttpCode;
use App\Modules\Common\Controllers\BaseAPIController;
use App\Modules\Users\Requests\StoreUserRequest;
use App\Modules\Users\Requests\UserLoginRequest;
use App\Modules\Users\Resources\JSON\AuthenticatedUserResource;
use App\Modules\Users\Resources\JSON\UserResource;
use Domain\Users\Entities\User;
use Domain\Users\Services\Interfaces\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserController extends BaseAPIController
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * @GET /users/
     * Renvoi la liste des utilisateurs
     * @return AnonymousResourceCollection
     */
    public function index() : AnonymousResourceCollection
    {
        return UserResource::collection(
            $this->userService->findAll()
        );
    }

    /**
     * @POST /users/
     * Création d'un utilisateur
     * @param StoreUserRequest $request
     * @return UserResource
     * @throws BadRequestException
     */
    public function store(StoreUserRequest $request) : AuthenticatedUserResource
    {
        $username = $request->validated()['username'] ?? '';
        $password = $request->validated()['password'] ?? '';

        try {
            return new AuthenticatedUserResource(
                $this->userService->createUser($username, $password)
            );
        } catch (\LogicException|\InvalidArgumentException $exception) {
            throw new BadRequestException($exception->getMessage());
        }
    }

    /**
     * @POST /users/login
     * Authentification d'un utilisateur
     * @param UserLoginRequest $request
     * @return AuthenticatedUserResource
     * @throws BadRequestException
     */
    public function login(UserLoginRequest $request) : AuthenticatedUserResource
    {
        $username = $request->validated()['username'] ?? '';
        $password = $request->validated()['password'] ?? '';

        try {
            return new AuthenticatedUserResource(
                $this->userService->login($username, $password)
            );
        } catch (\LogicException|\InvalidArgumentException $exception) {
            throw new BadRequestException($exception->getMessage());
        }
    }

    /**
     * @DELETE  /users/{user}
     * Supprime un utilisateur
     * @param User $user
     * @return Response
     */
    public function destroy(User $user) : Response
    {
        $this->userService->delete($user);
        return response(null, HttpCode::HTTP_NO_CONTENT);
    }
}

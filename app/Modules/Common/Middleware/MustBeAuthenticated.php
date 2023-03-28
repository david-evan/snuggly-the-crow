<?php

namespace App\Modules\Common\Middleware;

use Closure;
use Domain\Common\Exceptions\UnauthenticatedUserException;
use Domain\Common\Services\Interfaces\AuthenticationService;
use Illuminate\Auth\AuthenticationException;

class MustBeAuthenticated
{
    public function __construct(
        protected AuthenticationService $authenticationService
    )
    {
    }

    public function handle($request, Closure $next)
    {
        try {
            $this->authenticationService->mustBeAuthenticatedOrFail();
        } catch (UnauthenticatedUserException $exception) {
            throw new AuthenticationException($exception->getMessage());
        }
        return $next($request);
    }
}

<?php

namespace App\Modules\Common\Middleware;

use Closure;
use Domain\Common\Services\Interfaces\AuthenticationService;
use Illuminate\Http\Request;

class AuthenticateUserFromAPIKey
{
    public function __construct(
        protected AuthenticationService $authenticationService
    )
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $this->authenticationService->authenticateFromAPIKey($request->header('X-Api-Key') ?? '');
        return $next($request);
    }
}

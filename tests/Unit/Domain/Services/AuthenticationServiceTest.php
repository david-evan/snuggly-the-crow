<?php

namespace Domain\Services;

use Domain\Common\Exceptions\UnauthenticatedUserException;
use Domain\Common\Services\Interfaces\AuthenticationService;
use Domain\Users\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationServiceTest  extends TestCase
{
    use RefreshDatabase;

    private AuthenticationService $authenticationService;

    public function test_authenticateUserWithApiKey(): void
    {
        $this->authenticationService = $this->app->make(AuthenticationService::class);

        // given...
        /** @var User $user */
        $user = User::factory()->create();
        $user->updateLastLoginAndGenerateNewApiKey()->save();

        // when ...
        $authenticationSucess = $this->authenticationService->authenticateFromAPIKey($user->api_key);

        // then ...
        $this->assertTrue($authenticationSucess);
        $this->assertTrue($this->authenticationService->isAuthenticated());
        $this->assertTrue($this->authenticationService->getAuthenticatedUser()->id === $user->id);
        $this->assertTrue($this->authenticationService->getAuthenticatedUserOrFail()->id === $user->id);
    }

    public function test_mustBeAuthenticatedThrowExceptionWhenNotAuthenticated(): void
    {
        $this->authenticationService = $this->app->make(AuthenticationService::class);
        // given ... no authentication
        // then ...
        $this->expectException(UnauthenticatedUserException::class);
        // when ...
        $this->authenticationService->mustBeAuthenticatedOrFail();
        $this->authenticationService->getAuthenticatedUserOrFail();
        // and then ...
        $this->assertFalse($this->authenticationService->isAuthenticated());
    }

}

<?php

namespace Tests\Feature\Controllers;

use App\Library\SDK\Definitions\HttpCode;
use Tests\Feature\FeatureTestCase;

class WelcomeControllerTest extends FeatureTestCase
{

    public function test_welcomeMonitoringPage(): void
    {
        // when ...
        $response = $this->get(route('api.welcome'));

        // then ...
        $jsonResponseArray = json_decode($response->getContent(),true);

        $response->assertStatus(HttpCode::HTTP_OK);
        $this->assertArrayHasKey('apiVersion', $jsonResponseArray);
        $this->assertArrayHasKey('phpVersion', $jsonResponseArray);
        $this->assertArrayHasKey('webServer', $jsonResponseArray);
        $this->assertArrayHasKey('databaseConnected', $jsonResponseArray);
        $this->assertArrayHasKey('healthcheck', $jsonResponseArray);
        $this->assertArrayHasKey('routes', $jsonResponseArray);

        $this->assertTrue($jsonResponseArray['databaseConnected']);
        $this->assertTrue('OK' === $jsonResponseArray['healthcheck'] ?? '');
    }
}

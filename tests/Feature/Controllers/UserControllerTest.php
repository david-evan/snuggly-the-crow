<?php

namespace Tests\Feature\Controllers;


use Tests\Feature\FeatureTestCase;

class UserControllerTest extends FeatureTestCase
{

    public function test_welcomeMonitoringPage(): void
    {
        // when ...
        $response = $this->get(route('users.index'));
        // then ...

        //
    }
}

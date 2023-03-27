<?php

namespace Tests\Unit\Application\Requests;

use App\Library\APIProblem\Http\Requests\API\ApiRequest;
use Tests\TestCase;

class ApiRequestTest extends TestCase
{
    public function test_requestWantsJson(): void
    {
        $request = new class extends ApiRequest {};
        $this->assertTrue($request->wantsJson());
    }

    public function test_requestExpectsJson(): void
    {
        $request = new class extends ApiRequest {};
        $this->assertTrue($request->expectsJson());
    }
}

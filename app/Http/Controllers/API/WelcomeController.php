<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class WelcomeController extends APIController
{
    /**
     * Welcome message
     * @param Request $request
     * @param Router $router
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Router $router)
    {
        $routes = [];
        foreach ($router->getRoutes() as $route) {
            $routes[$route->getName()] = $route->uri();
        }

        $routes = array_filter($routes, fn($v) => strpos($v, '_ignition') === false);

        return response()->json([
            'routes' => $routes
        ]);
    }
}

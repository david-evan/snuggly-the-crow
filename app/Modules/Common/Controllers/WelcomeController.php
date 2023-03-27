<?php

namespace App\Modules\Common\Controllers;

use App\Library\SDK\Definitions\HttpCode;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;

class WelcomeController extends BaseAPIController
{
    /**
     * Welcome message
     * @param Request $request
     * @param Router $router
     * @return JsonResponse
     */
    public function index(Request $request, Router $router)
    {
        $healthcheck = 'OK';
        $routes = [];
        foreach ($router->getRoutes() as $route) {
            $routes[$route->getName() . ' (' . implode(', ', $route->methods()) . ')'] = $route->uri();
        }

        $routes = array_filter($routes, fn($v) => strpos($v, '_ignition') === false);

        try {
            DB::connection()->getPDO();
            $dbConnected = true;
        } catch (Exception $exception) {
            $dbConnected = false;
            $healthcheck = 'KO';
        }

        return response()->json([
            'appName' => config('app.name'),
            'apiVersion' => config('app.version'),
            'phpVersion' => phpversion(),
            'webServer' => $request->server('SERVER_SOFTWARE'),
            'databaseConnected' => $dbConnected,
            'healthcheck' => $healthcheck,
            'routes' => $routes
        ], $dbConnected ? HttpCode::HTTP_OK : HttpCode::HTTP_BAD_GATEWAY);
    }
}

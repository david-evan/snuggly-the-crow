<?php


namespace App\Library\APIProblem\Handlers;


use App\Library\APIProblem\Interfaces\RenderableException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class APIProblemsExceptionHandler extends Handler
{
    private const API_PROBLEMS_CONFIG_FILE = "api-problems";

    /**
     * APIProblemsExceptionHandler constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        if (config(self::API_PROBLEMS_CONFIG_FILE . '.report-renderable-exception') === false) {
            $this->dontReport[] = RenderableException::class;
        }
    }

    /**
     * Render an exception into an HTTP response.
     * @param Request $request
     * @param Throwable $exception
     * @return JsonResponse|\Illuminate\Http\Response|Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson()) {
            $exceptionToConvert = config(self::API_PROBLEMS_CONFIG_FILE . '.converter') ?? [];

            // Transformation des Exceptions en Problems (si existant)
            if (isset($exceptionToConvert[$this->getExceptionClassName($exception)])) {
                throw new $exceptionToConvert[$this->getExceptionClassName($exception)]($exception);
            }

            // Utilisation de la méthode render permettant de renvoyer l'exception
            if ($exception instanceof RenderableException) {
                return $exception->render();
            }

        }
        return parent::render($request, $exception);
    }

    /**
     * Retourne le nom complet d'une exception
     * @param Throwable $exception
     * @return string
     */
    protected function getExceptionClassName(Throwable $exception): string
    {
        return (new ReflectionClass($exception))->getName();
    }
}

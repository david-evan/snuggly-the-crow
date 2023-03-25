<?php


namespace App\Library\APIProblem\Handlers;


use App\Library\APIProblem\Interfaces\RenderableException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler;
use Throwable;

class APIProblemsExceptionHandler extends Handler
{
    private const PROBLEMS_CONFIG_FILE = "";
    /**
     * APIProblemsExceptionHandler constructor.
     * @param Container $container
     * @param APIProblemsConfigurator $APIProblemsConfigurator
     */
    public function __construct(Container $container, APIProblemsConfigurator $APIProblemsConfigurator)
    {
        parent::__construct($container);

        if(config('api-problems.report-') === false){
            $this->dontReport[] = RenderableException::class;
        }
    }

    /**
     * Render an exception into an HTTP response.
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($request->wantsJson()){
        $exceptionToConvert = config(APIProblemsServiceProvider::PROBLEMS_CONFIG_FILE.'.converter') ?? [];

        // Transformation des Exceptions en Problems (si existant)
        if(isset($exceptionToConvert[$this->getExceptionClassName($exception)])){
            throw new $exceptionToConvert[$this->getExceptionClassName($exception)]($exception);
        }

        // Utilisation de la méthode render permettant de renvoyer l'exception
        if($exception instanceof RenderableException){
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
    protected function getExceptionClassName(\Throwable $exception): string
    {
        return (new \ReflectionClass($exception))->getName();
    }
}

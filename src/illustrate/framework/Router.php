<?php


namespace illustrate;

use Illuminate\Support\Facades\App;
use illustrate\Exeptions\NotFoundException;
use Symfony\Component\Finder\Finder;
use illustrate\Application;
/**
 * Class Application Reactoor
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package Reactoor\Phoenix
 */

class Router
{
	private Application $application;
	private Request $request;
	private Response $response;
	private array $routeMap = [];

	public function __construct(Request $request , Response $response)
	{
		$this->request = $request;
		$this->response = $response;
	}

	public function get(string $url , $callback)
	{
		$this->routeMap[ 'get' ][ $url ] = $callback;
	}

	public function post(string $url , $callback)
	{
		$this->routeMap[ 'post' ][ $url ] = $callback;
	}

	public function bundle()
	{
		global $app;
		$find = new Finder();
		$file = $find->files()->in(__DIR__ . '/../../routes');
		foreach ($file as $item){
			return include $item->getRealPath();
		}
	}
    /**
     * @return array
     */
    public function getRouteMap(): array
    {
        return $this->routeMap ?? [];
    }

    public function getCallback()
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();
        // Trim slashes
        $url = trim($url, '/');

        // Get all routes for current request method
        $routes = $this->getRouteMap($method);

        $routeParams = false;

        // Start iterating registed routes
        foreach ($routes as $route => $callback) {
            // Trim slashes
            $route = trim($route, '/');
            $routeNames = [];

            if (!$route) {
                continue;
            }

            // Find all route names from route and save in $routeNames
            if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)) {
                $routeNames = $matches[1];
            }

            // Convert route name into regex pattern
            $routeRegex = "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($m) => isset($m[2]) ? "({$m[2]})" : '(\w+)', $route) . "$@";

            // Test and match current route against $routeRegex
            if (preg_match_all($routeRegex, $url, $valueMatches)) {
                $values = [];
                for ($i = 1; $i < count($valueMatches); $i++) {
                    $values[] = $valueMatches[$i][0];
                }
                $routeParams = array_combine($routeNames, $values);

                $this->request->setRouteParams($routeParams);
                return $callback;
            }
        }

        return false;
    }

    public function resolve()
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();
        $callback = $this->routeMap[$method][$url] ?? false;
        if (!$callback) {
            $callback = $this->getCallback();
            if ($callback === false) {
                throw new NotFoundException();
            }
        }
        if (is_string($callback)) {
			return Helpers::view($callback);
        }
        if (is_array($callback)) {
            /**
             * @var $controller illustrate\Controller
             */
            $controller = new $callback[0];
            $controller->action = $callback[1];
			Application::$app->controller = $controller;
            $middlewares = $controller->getMiddlewares();
            foreach ($middlewares as $middleware) {
                $middleware->execute();
            }
            $callback[0] = $controller;
        }
        return call_user_func($callback, $this->request, $this->response);
    }

}

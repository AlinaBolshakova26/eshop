<?php

namespace Core;

use Core\Exceptions\AppException;

class Router
{

    private $routes = [];

    private $namedRoutes = [];

    /**
     * @param string $method
     * @param string $path
     * @param callable|array $callback
     * @param string|null $name
     */

    public function addRoute(string $method, string $path, callable|array $callback, ?string $name = null): Route
    {

        $route = new Route($method, $path, $callback);
        $this->routes[] = $route;

        if ($name !== null)
        {
            $this->namedRoutes[$name] = $route;
        }

        return $route;

    }

    public function dispatch(): void
    {

        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) 
        {
            if ($route->matches($requestMethod,$requestUri))
            {
                $route->execute($requestUri);

                return;
            }    
        }

        throw new AppException
        (
            "Route not found: {$requestMethod} {$requestUri}",
            "Страница не найдена",
            404
        );

    }

    public function generateUrl(string $name, array $params = []): string
    {

        if (!isset($this->namedRoutes[$name]))
        {
            throw new \InvalidArgumentException("Маршрут с именем '{$name}' не найден");
        }

        $path = $this->namedRoutes[$name]->getPath();

        foreach($params as $key => $value)
        {

                $path = preg_replace('/\{' . $key . '(?::[^}]+)?\}/', $value, $path);

        }

        return $path;

    }
    
}
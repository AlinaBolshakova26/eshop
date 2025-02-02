<?php

namespace Core;

class Router
{
    private $routes = [];

    /**
     * @param string $method
     * @param string $path
     * @param callable $callback
     */

    public function addRoute(string $method, string $path, callable|array $callback): Route
    {
        $route = new Route($method, $path, $callback);
        $this->routes[] = $route;
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

        http_response_code(404);
        echo "Страница не найдена";
    }
}
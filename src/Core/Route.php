<?php

namespace Core;

class Route
{

    private string $method;
    private string $path;
    private  $callback;
    private array $paramNames = [];
    private string $pattern;

    public function __construct
    (
        string $method,
        string $path,
        callable|array $callback
    )
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->callback = $callback;
        $this->processPath();
    }

    private function processPath(): void
    {

        $this->pattern  = preg_replace_callback
        (
            '/\{(\w+)(?::([^}]+))?\}/',
            function($matches)
            {
                $this->paramNames[] = $matches[1]; //

                return isset($matches[2]) ? '(' . $matches[2] . ')' : '(\w+)';
            },
            $this->path,
        );

    }

    public function matches(string $method, string $uri): bool
    {
        return
            $this->method === $method
            &&
            preg_match('#^' . $this->pattern . '$#', $uri);
    }

    public function execute(string $uri):? array
    {

        preg_match('#^' . $this->pattern . '$#', $uri, $matches);
        array_shift($matches);

        $params = [];

        foreach($matches as $i => $match)
        {
            if (isset($this->paramNames[$i]))
            {
                $params[$this->paramNames[$i]] = $match;
            }
            else $params[] = $match;
        }

        if (is_array($this->callback))
        {
            [$controllerClass, $method] = $this->callback;

            $controller = new $controllerClass();

            return call_user_func_array([$controller, $method], $params);
        }

        return call_user_func_array($this->callback, $params);

    }
    
}

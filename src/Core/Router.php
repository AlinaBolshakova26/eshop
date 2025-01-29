<?php

namespace Core;

class Router
{
	private $routes = [];

	/**
	 *
	 *
	 * @param string $method
	 * @param string $path
	 * @param callable $callback
	 */
	public function addRoute($method, $path, $callback)
	{
		$this->routes[] = [
			'method' => strtoupper($method),
			'path' => $path,
			'callback' => $callback,
		];
	}

	public function dispatch()
	{
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		foreach ($this->routes as $route) {
			$pattern = '#^' . preg_quote($route['path'], '#') . '$#';

			if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
				array_shift($matches);

				call_user_func_array($route['callback'], $matches);
				return;
			}
		}

		http_response_code(404);
		echo "Страница не найдена";
	}
}

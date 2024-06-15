<?php

declare(strict_types=1);

namespace Src\Helpers;

use Src\Middleware\AuthMiddlewareDetection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, array $controller): self
    {
        $this->routes[] = [
            'path' => $path,
            'method' => strtoupper($method),
            'controller' => $controller,
            'middleware' => null
        ];

        return $this;
    }

    public function middleware($key): static
    {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;

        return $this;
    }

    public function dispatch(Request $request): Response
    {
        $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'])['path'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                if ($route['middleware'] !== null) {
                    $middleware = AuthMiddlewareDetection::from($route['middleware'])->detect();
                    $response = $middleware->handle($request);
                    if ($response !== null) {
                        return $response;
                    }
                }

                [$class, $function] = $route['controller'];

                return (new $class)->{$function}($request);
            }
        }
        return new Response('page not found');
    }
}